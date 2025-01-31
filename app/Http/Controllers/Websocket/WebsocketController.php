<?php

namespace App\Http\Controllers\Websocket;

use App\Http\Controllers\Controller;
use App\Services\JwtTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class WebsocketController extends Controller
{
    protected JwtTokenService $service;

    public function __construct(JwtTokenService $service)
    {
        $this->service = $service;
    }

    public function getPublicTokenJWT(): JsonResponse
    {
        $this->status = 'success';
        $this->code = 200;
        $this->dataJson = $this->service->generatePublicJWT(4);

        return $this->responseJsonApi();
    }

    public function getAuthTokenJWT(Request $request): JsonResponse
    {
        $this->status = 'success';
        $this->code = 200;
        $this->dataJson = $this->service->generateAuthJWT($request->user()->id, 4);

        return $this->responseJsonApi();
    }
}
