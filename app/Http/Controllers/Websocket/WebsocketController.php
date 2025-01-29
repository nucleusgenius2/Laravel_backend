<?php

namespace App\Http\Controllers\Websocket;

use App\Http\Controllers\Controller;
use App\Services\JwtTokenService;
use Illuminate\Http\Request;


class WebsocketController extends Controller
{
    public JwtTokenService $service;

    public function __construct(JwtTokenService $service)
    {
        $this->service = $service;
    }

    public function getPublicToken()
    {
        $this->status = 'success';
        $this->code = 200;
        $this->dataJson = $this->service->generatePublicJWT(4);

        return $this->responseJsonApi();
    }

    public function generateAuthJWT(Request $request)
    {
        $this->status = 'success';
        $this->code = 200;
        $this->dataJson = $this->service->generateAuthJWT($request->user()->id, 4);

        return $this->responseJsonApi();
    }
}
