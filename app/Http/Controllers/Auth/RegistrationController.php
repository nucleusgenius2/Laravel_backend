<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use App\Models\UserParam;
use App\Services\GenerateUniqueString;
use App\Services\UserService;
use App\Traits\StructuredResponse;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegistrationController
{
    use StructuredResponse;

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
