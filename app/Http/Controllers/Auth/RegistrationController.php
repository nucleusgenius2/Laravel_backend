<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequest;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;
use Stevebauman\Location\Facades\Location;


class RegistrationController extends Controller
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function registration(RegistrationRequest $request): JsonResponse
    {
        $data = $request->validated();

        $ip = request()->header('X-Forwarded-For') ?? request()->header('CF-Connecting-IP') ?? request()->ip();
        $location = Location::get($ip);

        $userData = $this->service->createUser(data: $data, country: $location->countryCode ?? 'RU');

        if ( $userData['status'] ){
            $this->status = 'success';
            $this->code = 200;
            $this->dataJson = $userData['returnData'];
            $this->message = 'Регистрация прошла успешно';
        }
        else {
            $this->code = 500;
            $this->message = 'Ошибка при регистрации: ' .  $userData['error'];
        }

        return $this->responseJsonApi();
    }
}
