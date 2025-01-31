<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(FiatCoinSeeder::class);
        $this->call(ConfigWinmoveSeeder::class);
        $this->call(WinnerSeeder::class);
        $this->call(AdvertSeeder::class);
    }
}
