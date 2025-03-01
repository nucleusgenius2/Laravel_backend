<?php

namespace App\Http\Controllers\State;

use App\DTO\DataObjectDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\CountRequest;
use App\Http\Requests\PlayGameRequest;
use App\Models\Game;
use App\Models\PlayGame;
use App\Services\GameService;
use App\Services\GenerateUniqueString;
use App\Services\User\UserService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PlayGameController extends Controller
{
    protected GameService $service;

    public function __construct(GameService $service){
        $this->service = $service;
    }


    public function index(PlayGameRequest $request): JsonResponse
    {
        $data = $request->validated();

        $dataObjectDto = $this->service->getPlayGameData(data: $data, user: $request->user() );

        $this->status = 'success';
        $this->code = 200;
        $this->dataJson = $dataObjectDto->data ;

        return $this->responseJsonApi();
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


}
