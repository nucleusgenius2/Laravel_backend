<?php

namespace Database\Seeders;

use App\Models\FiatCoin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FiatCoinSeeder extends Seeder
{
    public function run()
    {
        if(!FiatCoin::exists()) {
            FiatCoin::insert([
                [
                    'img' => '',
                    'code' => 'RUB',
                    'name' => 'Russian Ruble',
                    'country' => 'RUSSIAN FEDERATION',
                    'type' => 'fiat'
                ],
                [
                    'img' => '',
                    'code' => 'USD',
                    'name' => 'US Dollar',
                    'country' => 'UNITED STATES',
                    'type' => 'fiat'
                ],
                [
                    'img' => '',
                    'code' => 'BTC',
                    'name' => 'Bitcoin',
                    'type' => 'crypto',
                    'country' => '',
                ],
                [
                    'img' => '',
                    'code' => 'USDT',
                    'name' => 'Tether',
                    'type' => 'crypto',
                    'country' => '',
                ],
                [
                    'img' => '',
                    'code' => 'ETH',
                    'name' => 'Ethereum',
                    'type' => 'crypto',
                    'country' => '',
                ],
                [
                    'img' => '',
                    'code' => 'BNB',
                    'name' => 'Binance Coin',
                    'type' => 'crypto',
                    'country' => '',
                ]
            ]);

        }
    }
}
