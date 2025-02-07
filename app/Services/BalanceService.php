<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Balance;
use App\Models\Countries;
use App\Models\FiatCoin;
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
            'balances.to_date',
            'balances.count',
            'balances.count',
            'balances.nominal',
            'fiat_coin.name as currency_name',
            'fiat_coin.img as currency_img',
            'fiat_coin.code as currency_code',
            'fiat_coin.id as currency_id',
        )
            ->where('accounts.user_id', $user->id)
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
            ];

            if(!isset($item->to_date)){ unset($item->to_date); }
            if(!isset($item->nominal)){ unset($item->nominal); }
            if($item->count ==='0.00'){ unset($item->count); }

            // Убираем ненужные поля
            unset($item->currency_name, $item->currency_img, $item->currency_code, $item->currency_id);

            return $item;
        });

        return $balance;

    }


    public function addBalance(string $currency, User $user): array
    {
        DB::beginTransaction();

        try {
            $newCurrency= FiatCoin::select('id','code')->where('code', $currency)->first();

            if(!$newCurrency){
                throw new \Exception('Не валидный код валюты: ' . $currency);
            }

            $availableCurrencies = $this->service->getUserCurrencies(user: $user);

            if( !$availableCurrencies ){
                throw new \Exception('Данная валюта не доступна в вашей стране ' . $currency);
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
                ['amount' => 0, 'account_id' => $accountMain->id,'count' => 0 ],
                ['amount' => 0, 'account_id' => $accountMintwin->id,'count' => 0],
            ];

            Balance::insert($balance);

            DB::commit();


            return [
                'status' => true,
            ];

        }
        catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => false,
                'error' => $e->getMessage(),
            ];

        }
    }

    public function setDefault(string $currency, User $user): array
    {
        DB::beginTransaction();

        try {
            $newCurrency = FiatCoin::select('id','code','type')->where('code', $currency)->first();

            if(!$newCurrency ){
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


            $cost = $this->convert(currencyPrev: $balanceOldBonusData->code, currencyNext: $newCurrency->code);
            if (!$cost){
                throw new \Exception('Данные о курсе валют не получены: ' . $balanceOldBonusData->code.' '.$newCurrency->code);
            }

            //округление 8 знаков для крипты и 2 для обычных валют
            if($newCurrency->type==='crypto' || $balanceOldBonusData->type==='crypto' ) {
                $cost = sprintf('%.8f', $cost);
                $newAmount = bcmul((string)$balanceOldBonusData->amount, (string)$cost, 8);
            }
            else{
                $newAmount = bcmul((string)$balanceOldBonusData->amount, (string)$cost, 2);
            }

            log::info('итог'.$newAmount);

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

            return [
                'status' => true
            ];
        }
        catch (\Exception $e) {
            DB::rollBack();

            return [
                'status' => false,
                'error' => $e->getMessage(),
            ];

        }
    }
}
