<?php

namespace App\Http\Controllers\State;

use App\Http\Controllers\Controller;
use App\Http\Requests\CountRequest;
use App\Models\PlayGame;
use App\Services\GameService;
use Illuminate\Http\JsonResponse;

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
}
