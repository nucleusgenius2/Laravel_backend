<?php

namespace Database\Seeders;

use App\Models\FiatCoin;
use App\Models\Game;
use App\Models\PlayGame;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WinnerSeeder extends Seeder
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function run()
    {
        if(!PlayGame::exists()) {
            DB::beginTransaction();
            try {
                $data = [
                    'phone' => '788899922',
                    'email' => 'testgame@mail.ru',
                    'password' => '123456',
                    'currency' => 'rub',
                ];

                $userData = $this->service->createUser($data);

                $game = Game::create([
                    'gameId' => 1,
                    'type' => 'card',
                    'img' => '',
                ]);

                PlayGame::create([
                    'gameId' => $game->gameId,
                    'date_play' => Carbon::now(),
                    'bet' => 2,
                    'win' => 1,
                    'ratio' => 1,
                    'user_id' => $userData['fullUserData']['user']->id,
                ]);

                $data = [
                    'phone' => '7822299988',
                    'email' => 'testgame2@mail.ru',
                    'password' => '123456',
                    'currency' => 'usd'
                ];

                $userData = $this->service->createUser($data);

                $game = Game::create([
                    'gameId' => 2,
                    'type' => 'slots',
                    'img' => '',
                ]);

                PlayGame::create([
                    'gameId' => $game->gameId,
                    'date_play' => Carbon::now(),
                    'bet' => 1,
                    'win' => 2,
                    'ratio' => 2,
                    'user_id' => $userData['fullUserData']['user']->id,
                ]);

                DB::commit();
            }
            catch (\Exception $e) {
                DB::rollBack();

                log::info($e);
            }
        }
    }
}
