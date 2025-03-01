<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserLogin;
use App\Events\WinnersWebsocketSend;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Services\User\LoginService;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    protected LoginService $service;

    public function __construct(LoginService $service)
    {
        $this->service = $service;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $dataArrayDto = $this->service->login(data: $data, request: $request);

        if($dataArrayDto->status){
            $this->status = 'success';
            $this->code = 200;
            $this->dataJson = $dataArrayDto->data;
            $this->message = 'Вход успешен';
        }
        else{
            $this->code = $dataArrayDto->code;
            $this->message = $dataArrayDto->data;
        }

        return $this->responseJsonApi();
    }

}
