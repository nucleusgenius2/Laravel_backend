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
                    'currencies' => json_encode(['RUB','BTC','USDT','ETH'])
                ],
                [
                    'code' => 'US',
                    'phone_prefix' => 1,
                    'title' => 'United States of America',
                    'currencies' => json_encode(['USD','BTC','USDT','ETH'])
                ]
            ]);
        }
    }
}
