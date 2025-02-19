<?php

namespace App\Services;

use App\DTO\DataEmptyDto;
use App\DTO\DataObjectDto;
use App\Models\Account;
use App\Models\Balance;
use App\Models\Countries;
use App\Models\FiatCoin;
use App\Models\FsBalance;
use App\Models\User;
use App\Models\UserParam;
use App\Traits\CurrencyRateProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BalanceService
{
    use CurrencyRateProvider;

    protected UserFiatService $service;

    public function __construct(UserFiatService $service){
        $this->service = $service;
    }

    public function getBalance(User $user): Collection
    {
        $balance = Account::select(
            'accounts.type',
            'balances.amount',
            'fiat_coin.name as currency_name',
            'fiat_coin.img as currency_img',
            'fiat_coin.code as currency_code',
            'fiat_coin.id as currency_id',
            'fiat_coin.type as currency_type',
        )
            ->where([['accounts.user_id', $user->id],['accounts.type', '!=', 'fsbonus']])
            ->rightJoin('balances', 'accounts.id', '=', 'balances.account_id')
            ->join('fiat_coin', 'accounts.fiat_coin', '=', 'fiat_coin.id')
            ->get();

        $mainCurrencyId = UserParam::select('currency_id')->where('id', $user->id)->first();


        $balance = $balance->map(function ($item) use ($mainCurrencyId) {

            $item->default = $item->currency_id == $mainCurrencyId->currency_id;

            $item->currency = [
                'name' => $item->currency_name,
                'img'  => $item->currency_img,
                'code' => $item->currency_code,
                'type' => $item->currency_type,
            ];

            //показать счет с mintwin только для главной валюты
            if( $item->type==="mintwin"){
                if ($item->currency_id !==$mainCurrencyId->currency_id){
                    return null;
                }
            }

            unset($item->currency_name, $item->currency_img, $item->currency_code, $item->currency_id);

            return $item;
        })->filter()->values();

        return $balance;

    }

    public function getBalanceFs(User $user): DataObjectDto
    {
        $data = FsBalance::select(
            'fs_balances.count',
            'fs_balances.nominal',
            'fs_balances.to_date',
            'bonus.name'
        )
            ->where('user_id', $user->id)
            ->join('bonus', 'bonus.id', '=', 'fs_balances.bonus_id')
            ->get();
        return new DataObjectDto(status: true, data: $data);
    }


    public function addBalance(string $currency, User $user): DataEmptyDto
    {
        DB::beginTransaction();

        try {
            $newCurrency= FiatCoin::select('id','code')->where('code', $currency)->first();

            if(!$newCurrency){
                throw new \Exception('Не валидный код валюты: ' . $currency);
            }

            $dataArrayDto = $this->service->getUserCurrencies(user: $user);

            if( !$dataArrayDto->status ){
                throw new \Exception('Данная валюта не доступна в вашей стране ' . $currency);
            }
            else{
                $availableCurrencies =  $dataArrayDto->data;
            }

            if ( !in_array($newCurrency->code, $availableCurrencies)) {
                throw new \Exception('Счет с данной валютой уже есть или валюта недоступна в вашей стране ' . $currency);
            }


            $accountMain = Account::create([
                'user_id' => $user->id,
                'type' => 'main',
                'fiat_coin' => $newCurrency->id
            ]);

            $accountMintwin = Account::create([
                'user_id' => $user->id,
                'type' => 'mintwin',
                'fiat_coin' => $newCurrency->id
            ]);

            $balance = [
                ['amount' => 0, 'account_id' => $accountMain->id ],
                ['amount' => 0, 'account_id' => $accountMintwin->id],
            ];

            Balance::insert($balance);

            DB::commit();

            return new DataEmptyDto(status: true);
        }
        catch (\Exception $e) {
            DB::rollBack();

            return new DataEmptyDto(status: false, error: $e->getMessage() );
        }
    }

    public function setDefault(string $currency, User $user): DataEmptyDto
    {
        DB::beginTransaction();

        try {
            $newCurrency = FiatCoin::select('id','code','type')->where('code', $currency)->first();

            if(!$newCurrency){
                throw new \Exception('Не валидный код валюты: ' . $currency);
            }

            if(!Account::where([['user_id', $user->id],['fiat_coin' , $newCurrency->id]])->exists()){
                throw new \Exception('у юзера нет счета с данной валютой ' . $currency);
            }

            $update = UserParam::where('id', $user->id)->update([
                'currency_id' => $newCurrency->id,
            ]);

            $balanceOldBonusData = Account::select(
                    'accounts.id as account_id',
                    'balances.amount as amount',
                    'balances.id as balances_id',
                    'fiat_coin.code as code',
                    'fiat_coin.type as type',
                )
                ->where([
                    ['accounts.user_id', $user->id],
                    ['accounts.type', 'bonus']
                ])
                ->join('balances','accounts.id','=','balances.account_id')
                ->join('fiat_coin','accounts.fiat_coin','=','fiat_coin.id')
                ->first();


            //конвертация валют
            $cost = $this->convert(currencyPrev: $balanceOldBonusData->code, currencyNext: $newCurrency->code);
            if (!$cost){
                throw new \Exception('Данные о курсе валют не получены: ' . $balanceOldBonusData->code.' '.$newCurrency->code);
            }

            $newAmount = $this->convertTotal(cost: $cost, amount: $balanceOldBonusData->amount);


            /*
            //округление 8 знаков для крипты и 2 для обычных валют
            if($newCurrency->type==='crypto' || $balanceOldBonusData->type==='crypto') {
                log::info('type '. $newCurrency->type);
                $cost = sprintf('%.12f', $cost);
                $newAmount = bcmul((string)$balanceOldBonusData->amount, (string)$cost, 12);
            }
            else{
                $newAmount = bcmul((string)$balanceOldBonusData->amount, (string)$cost, 2);
            }
            */

            $accountUpdate = Account::where([
                ['user_id', $user->id],
                ['type', 'bonus']
            ])->update([
                'fiat_coin' => $newCurrency->id,
            ]);

            $balanceUpdate = Balance::where(
                'account_id', $balanceOldBonusData->account_id,
            )->update([
                'amount' => $newAmount
            ]);

            DB::commit();

            return new DataEmptyDto(status: true);
        }
        catch (\Exception $e) {
            DB::rollBack();

            return new DataEmptyDto(status: false, error: $e->getMessage() );
        }
    }
}
