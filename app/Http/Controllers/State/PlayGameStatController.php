<?php

namespace App\Http\Controllers\State;

use App\DTO\DataObjectDto;
use App\Http\Controllers\Controller;
use App\Services\GameService;
use App\Services\PlayGameStatService;
use Illuminate\Http\Request;

class PlayGameStatController extends Controller
{
    protected PlayGameStatService $service;

    public function __construct(PlayGameStatService $service){
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $dataObjectDto = $this->service->getStatGame(user: $request->user());

        if( $dataObjectDto->status){
            $this->status = 'success';
            $this->code = 200;
            $this->dataJson = $dataObjectDto->data;
        } else{
            $this->code = 400;
        }

        return $this->responseJsonApi();
    }
}
