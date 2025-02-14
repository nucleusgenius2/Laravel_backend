<?php

namespace Database\Seeders;

use App\Models\ConfigWinmove;
use App\Models\Countries;
use Illuminate\Database\Seeder;

class CountriesSeeder extends Seeder
{
    public function run()
    {
        if(!Countries::exists()) {
            Countries::insert([
                [
                    'code' => 'RU',
                    'phone_prefix' => 7,
                    'title' => 'Russian Federation',
                    'currencies' => json_encode(['RUB', 'BTC', 'USDT', 'ETH', 'BNB'])
                ],
                [
                    'code' => 'US',
                    'phone_prefix' => 1,
                    'title' => 'United States of America',
                    'currencies' => json_encode(['USD', 'BTC', 'USDT', 'ETH', 'BNB'])
                ],
                [
                    'code' => 'AT',
                    'phone_prefix' => 43,
                    'title' => 'Austria',
                    'currencies' => json_encode(['EUR', 'BTC', 'USDT', 'BNB'])
                ],
                [
                    'code' => 'DE',
                    'phone_prefix' => 49,
                    'title' => 'Germany',
                    'currencies' => json_encode(['EUR', 'BTC', 'USDT', 'BNB'])
                ],
                [
                    'code' => 'EE',
                    'phone_prefix' => 372,
                    'title' => 'Estonia',
                    'currencies' => json_encode(['EUR', 'BTC', 'USDT', 'BNB'])
                ],
                [
                    'code' => 'GB',
                    'phone_prefix' => 44,
                    'title' => 'Great Britain',
                    'currencies' => json_encode(['GBP', 'BTC', 'USDT', 'BNB'])
                ],
                [
                    'code' => 'LV',
                    'phone_prefix' => 371,
                    'title' => 'Latvia',
                    'currencies' => json_encode(['EUR', 'BTC', 'USDT', 'BNB'])
                ],
                [
                    'code' => 'MD',
                    'phone_prefix' => 373,
                    'title' => 'Moldova',
                    'currencies' => json_encode(['EUR', 'BTC', 'USDT'])
                ],
                [
                    'code' => 'NO',
                    'phone_prefix' => 47,
                    'title' => 'Norway',
                    'currencies' => json_encode(['EUR', 'BTC', 'USDT', 'BNB'])
                ],
                [
                    'code' => 'UA',
                    'phone_prefix' => 380,
                    'title' => 'Ukraine',
                    'currencies' => json_encode(['EUR', 'BTC', 'USDT', 'BNB', 'RUB', 'UAH'])
                ],
            ]);

        }
    }
}
