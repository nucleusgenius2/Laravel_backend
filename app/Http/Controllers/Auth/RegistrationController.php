<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use App\Models\UserParam;
use App\Traits\StructuredResponse;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RegistrationController
{
    use StructuredResponse;

    public function registration(RegistrationRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => $data['password'],
            'created_at' => Carbon::now(),
        ]);

        UserParam::create([
            'id' => $data['name'],
            'level' => 1,
            'password' => $data['password'],
            'created_at' => Carbon::now(),
        ]);

        $token = $user->createToken('token', ['permission:user'])->plainTextToken;

        $this->status = 'success';
        $this->code = 200;
        $this->dataJson = $token;
        $this->message = 'Регистрация прошла успешно';

        return $this->responseJsonApi();
    }
}
