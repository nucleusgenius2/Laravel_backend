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
                    'country' => 'RUSSIAN FEDERATION'
                ],
                [
                    'img' => '',
                    'code' => 'USD',
                    'name' => 'US Dollar',
                    'country' => 'UNITED STATES'
                ]
            ]);
        }
    }
}
