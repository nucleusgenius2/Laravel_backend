<?php

namespace App\Services;

use App\DTO\DataArrayDto;
use App\DTO\DataObjectDto;
use App\Models\Account;
use App\Models\Countries;
use App\Models\FiatCoin;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserFiatService
{
    /**
     * Возвращаем список доступных валют для создания нового счета у пользователя
     * @param User $user
     * @return array|null
     */
    public function getUserCurrencies(User $user): DataArrayDto
     {
         $usedCurrencies = Account::select('fiat_coin.code')
             ->where('user_id', $user->id)
             ->join('fiat_coin','accounts.fiat_coin', '=', 'fiat_coin.id' )
             ->pluck('code')->toArray();

         $usedCurrencies = array_unique($usedCurrencies);

         $currencies = Countries::select('currencies')->where('code',  $user->params->country)->first();
         if( $currencies ){
             $currenciesArray = json_decode($currencies->currencies, true);

             //берем доступные для страны валюты, но исключаем те валюты, которые уже использует пользователь
             return new DataArrayDto(status: true, data: array_diff($currenciesArray, $usedCurrencies));
         }
         else{
             return new DataArrayDto(status: false);
         }
     }

    public function getCurrencies(): DataObjectDto
    {
        $fiats = FiatCoin::where('type', "fiat")->get();

        return new DataObjectDto(status: true, data: $fiats);
    }

    public function showCurrencies(string $code): DataObjectDto
    {
        if (!ctype_alnum($code)) {
            return new DataObjectDto(status: false, error: 'Не валидные данные');
        } else {
            $currency = FiatCoin::where('code', $code)->first();

            if($currency){
                return new DataObjectDto(status: true, data: $currency);
            }
            else {
                return new DataObjectDto(status: false,  error: 'Валюта не найдена');
            }

        }
    }

}
