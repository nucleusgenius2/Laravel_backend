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

    public function getBalance(): Collection
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
            ->where('accounts.user_id', Auth::user()->id)
            ->rightJoin('balances', 'accounts.id', '=', 'balances.account_id')
            ->join('fiat_coin', 'accounts.fiat_coin', '=', 'fiat_coin.id')
            ->get();

        $mainCurrencyId = UserParam::select('currency')->where('id', Auth::user()->id)->first();


        $balance = $balance->map(function ($item) use ($mainCurrencyId) {

            $item->default = $item->currency_id == $mainCurrencyId->currency;

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


    public function addBalance(array $data): array
    {
        $user = Auth::user();

        DB::beginTransaction();

        try {
            $fiat = FiatCoin::select('id')->where('code', $data['currency'])->first();

            if(!$fiat){
                throw new \Exception('Не валидный код валюты: ' . $data['currency']);
            }

            if(Account::where([['user_id', $user->id],['fiat_coin' , $fiat->id]])->exists()){
                throw new \Exception('счет с данной валютой уже есть ' . $data['currency']);
            }


            $accountMain = Account::create([
                'user_id' => $user->id,
                'type' => 'main',
                'fiat_coin' => $fiat->id
            ]);

            $accountMintwin = Account::create([
                'user_id' => $user->id,
                'type' => 'mintwin',
                'fiat_coin' => $fiat->id
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
            $fiat = FiatCoin::select('id','code')->where('code', $currency)->first();

            if(!$fiat){
                throw new \Exception('Не валидный код валюты: ' . $currency);
            }

            if(!Account::where([['user_id', $user->id],['fiat_coin' , $fiat->id]])->exists()){
                throw new \Exception('у юзера нет счета с данной валютой ' . $currency);
            }

            $currencies = Countries::select('currencies')->where('code', $user->params->country)->first();
            if( $currencies ){
                $currenciesArray = json_decode($currencies->currencies, true);

                if (!in_array($fiat->code, $currenciesArray)) {
                    throw new \Exception('В стране юзера не доступна данная валюта или она уже выбрана: ' . $currency);
                }
            }
            else{
                throw new \Exception('В стране юзера не доступна данная валюта или она уже выбрана: ' . $currency);
            }

            $update = UserParam::where('id', $user->id)->update([
                'currency_id' => $fiat->id,
            ]);

            $balanceOldBonusData = Account::select(
                    'accounts.id as account_id',
                    'balances.amount as amount',
                    'balances.id as balances_id',
                    'fiat_coin.code as code',
                )
                ->where([
                    ['accounts.user_id', $user->id],
                    ['accounts.type', 'bonus']
                ])
                ->join('balances','accounts.id','=','balances.account_id')
                ->join('fiat_coin','accounts.id','=','fiat_coin.id')
                ->first();

            log::info($balanceOldBonusData);
            $cost = $this->convert($balanceOldBonusData->code, $fiat->code);

            log::info($cost);

/*
            Account::where([
                ['user_id', $user->id],
                ['fiat_coin', $fiat->id],
                ['type', 'bonus']
            ])->update([
                'currency_id' => $fiat->id,
            ]);
*/
           //тут мы должны с конвертировать баланс с бонусного счета в выбранную по умолчанию валюту


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
