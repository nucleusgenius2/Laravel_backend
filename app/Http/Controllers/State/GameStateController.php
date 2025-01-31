<?php

namespace App\Http\Controllers\State;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;


class GameStateController extends Controller
{
    public function index() : JsonResponse
    {
        $this->status = 'success';
        $this->code = 200;
        $this->dataJson = [
            'game' => [
            ],
        ];

        return $this->responseJsonApi();
    }

}
