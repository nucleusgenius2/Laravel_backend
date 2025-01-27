<?php

namespace Database\Seeders;

use App\Models\ConfigWinmove;
use App\Models\FiatCoin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigWinmoveSeeder extends Seeder
{
    public function run()
    {
        if(!ConfigWinmove ::exists()) {
            ConfigWinmove::insert([
                [
                    'param' => 'reg_bonus',
                    'is_active' => 1,
                    'val' => 100,
                ],
            ]);
        }
    }
}
