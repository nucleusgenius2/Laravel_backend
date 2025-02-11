<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait CurrencyRateProvider
{
    /**
     * Возвращает стоимость $currencyPrev в новой валюте $currencyNext
     * @param string $currencyPrev
     * @param string $currencyNext
     * @return float|null
     */
    public function convert(string $currencyPrev, string $currencyNext): float|null
    {
        $url = config("winmove.converting_currency_uri").'/v1/currencies/'.strtolower($currencyPrev).'.json';

        try {
            $response = Http::withOptions([
                'verify' => false, // временно отключили проверку SSL
            ])->get($url);

            return $response[strtolower($currencyPrev)][strtolower( $currencyNext)];
        }
        catch (\Exception $e) {
            Log::error('Ошибка запроса на конвертацию валют'.$e);
            return null;
        }
    }

    public function convertTotal(string $cost, string $amount): string
    {
        log::info( 'конвертация');
        log::info( 'курс '.$cost);
        log::info( 'сумма '.$amount);
        //округление 8 знаков для крипты и 2 для обычных валют
        $cost = sprintf('%.12f', $cost);
        return bcmul((string)$amount, (string)$cost, 12);
    }
}
