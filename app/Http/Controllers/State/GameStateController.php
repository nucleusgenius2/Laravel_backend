<?php

namespace App\Http\Controllers\State;

use App\Services\UserService;
use App\Traits\StructuredResponse;

class GameStateController
{
    use StructuredResponse;

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
