<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BonusTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('bonus')->insert([
            ['id' => 2, 'name' => '100 FS ЗА РЕГИСТРАЦИЮ', 'type' => 'reg_bonus', 'amount' => 0.00, 'valid' => null, 'bonus_type' => 'fsbonus', 'bonus_count' => 100, 'status' => 1, 'bonus_nominal' => null, 'bonus' => null],
            ['id' => 3, 'name' => 'Первый депозит', 'type' => 'first_deposit', 'amount' => 30.00, 'valid' => null, 'bonus_type' => 'fsbonus', 'bonus_count' => 25, 'status' => 1, 'bonus_nominal' => null, 'bonus' => 100],
            ['id' => 4, 'name' => 'Первый депозит', 'type' => 'first_deposit', 'amount' => 100.00, 'valid' => null, 'bonus_type' => 'fsbonus', 'bonus_count' => 25, 'status' => 1, 'bonus_nominal' => null, 'bonus' => 120],
            ['id' => 5, 'name' => 'Первый депозит', 'type' => 'first_deposit', 'amount' => 300.00, 'valid' => null, 'bonus_type' => 'fsbonus', 'bonus_count' => 25, 'status' => 1, 'bonus_nominal' => null, 'bonus' => 150],
            ['id' => 6, 'name' => 'Второй депозит', 'type' => 'second_deposit', 'amount' => 30.00, 'valid' => null, 'bonus_type' => 'fsbonus', 'bonus_count' => 50, 'status' => 1, 'bonus_nominal' => null, 'bonus' => 150],
            ['id' => 7, 'name' => 'Второй депозит', 'type' => 'second_deposit', 'amount' => 100.00, 'valid' => null, 'bonus_type' => 'fsbonus', 'bonus_count' => 50, 'status' => 1, 'bonus_nominal' => null, 'bonus' => 170],
            ['id' => 8, 'name' => 'Второй депозит', 'type' => 'second_deposit', 'amount' => 300.00, 'valid' => null, 'bonus_type' => 'fsbonus', 'bonus_count' => 50, 'status' => 1, 'bonus_nominal' => null, 'bonus' => 180],
        ]);
    }
}
