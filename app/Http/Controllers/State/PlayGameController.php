<?php

namespace App\Http\Controllers\State;

use App\Http\Controllers\Controller;
use App\Http\Requests\CountRequest;
use App\Models\Game;
use App\Models\PlayGame;
use App\Services\GameService;
use App\Services\GenerateUniqueString;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlayGameController extends Controller
{
    protected GameService $service;

    public function __construct(GameService $service){
        $this->service = $service;
    }

    public function indexTable(CountRequest $request): JsonResponse
    {
        $data = $request->validated();

        $winners = $this->service->getTableWinner($data['count']);

        $this->status = 'success';
        $this->code = 200;
        $this->dataJson = $winners;

        return $this->responseJsonApi();
    }

    public function indexSlider(CountRequest $request): JsonResponse
    {
        $data = $request->validated();

        $winners = $this->service->getTableSlider($data['count']);

        $this->status = 'success';
        $this->code = 200;
        $this->dataJson = $winners;

        return $this->responseJsonApi();
    }

    public function show(int $id): JsonResponse
    {
        if ($id > 0) {
            $winners = $this->service->getPlayGame($id);

            $this->status = 'success';
            $this->code = 200;
            $this->dataJson = $winners;
        }

        return $this->responseJsonApi();
    }

    //тестовый метод для отладки, потом убрать
    public function createTest(UserService $service): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = [
                'phone' => '788899922',
                'email' => 'testgame@mail.ru',
                'password' => '123456',
                'currency' => 'rub',
            ];

            $userData = $service->createUser($data);

            $game = Game::create([
                'gameId' => GenerateUniqueString::generateName(10),
                'type' => 'minigame',
                'img' => '',
                'title' => 'Рога и копыта',
            ]);

            PlayGame::create([
                'gameId' => $game->gameId,
                'date_play' => Carbon::now(),
                'bet' => 2,
                'win' => 1,
                'ratio' => 1,
                'user_id' => $userData['fullUserData']['user']->id,
            ]);
            DB::commit();
            $this->status = 'success';
            $this->code = 200;
        }
        catch (\Exception $e) {
            DB::rollBack();
            $this->message = $e;

        }


        return $this->responseJsonApi();
    }


}
