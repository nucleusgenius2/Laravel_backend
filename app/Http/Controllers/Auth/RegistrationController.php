<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;


class RegistrationController extends Controller
{
    public UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function registration(RegistrationRequest $request): JsonResponse
    {
        $data = $request->validated();

        $userData = $this->service->createUser($data);

        if ( $userData['status'] ){
            $this->status = 'success';
            $this->code = 200;
            $this->dataJson = $userData['token'];
            $this->message = 'Регистрация прошла успешно';
        }
        else {
            $this->code = 500;
            $this->message = 'Ошибка при регистрации: ' .  $userData['error'];
        }

        return $this->responseJsonApi();
    }
}
