<?php

namespace App\Http\Controllers\Auth;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Traits\StructuredResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;


class LoginController extends FormRequest
{
    use StructuredResponse;
    public function login(LoginRequest $request): JsonResponse
    {

        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if ($user) {
            if (Hash::check($data['password'], $user->password)) {
                $token = $user->createToken('token', ['permission:user'])->plainTextToken;

                $dataUser = [
                    'token' => $token,
                    'user' => $user->name,
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


        return $this->responseJsonApi();
    }

}
