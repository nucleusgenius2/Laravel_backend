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
}
