<?php

namespace App\Services;

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
    public function getUserCurrencies(User $user): array|null
     {
         $usedCurrencies = Account::select('fiat_coin.code')
             ->where('user_id',  $user->id)
             ->join('fiat_coin','accounts.fiat_coin', '=', 'fiat_coin.id' )
             ->pluck('code')->toArray();

         $usedCurrencies = array_unique($usedCurrencies);

         $currencies = Countries::select('currencies')->where('code',  $user->params->country)->first();
         if( $currencies ){
             $currenciesArray = json_decode($currencies->currencies, true);
log::info($currenciesArray);
             log::info($usedCurrencies);
             //Исключаем те валюты, которые уже использует пользователь
             return $availableCurrencies = array_diff($currenciesArray, $usedCurrencies);
         }
         else{
             return null;
         }
     }
}
