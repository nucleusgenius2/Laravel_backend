<?php

namespace App\Http\Controllers\State;

use App\Http\Controllers\Controller;


class GameStateController extends Controller
{
    public function index()
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
