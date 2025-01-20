<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Traits\StructuredResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LoginController
{
    use StructuredResponse;
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validated->fails()) {
            $this->message = $validated ->errors();
        } else {
            $data = $validated->valid();

            $user = User::where('email', $data['email'])->first();

            if ($user) {
                log::info($data['password']);
                if (Hash::check($data['password'], $user->password)) {
                    $token = $user->createToken('token', ['permission:user'])->plainTextToken;

                    $dataUser = [
                        'token' => $token,
                        'user' => $user->email,
                    ];

                    $this->status = 'success';
                    $this->code = 200;
                    $this->dataJson = $dataUser;
                    $this->message = 'Вход успешен';

                } else {
                    $this->message = 'Пароль не совпадает';
                }
            } else {
                $this->message = 'Email не найден';
            }
        }

        return $this->responseJsonApi();
    }

}
