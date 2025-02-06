<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;

use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::select('users.*', 'user_params.*','fiat_coin.code as main_currency',)
            ->join('user_params', 'users.id', '=', 'user_params.id')
            ->join('fiat_coin', 'fiat_coin.id', '=', 'user_params.currency_id')
            ->where('users.email', $data['email'])
            ->first();

        if ($user) {
            if (Hash::check($data['password'], $user->password)) {
                $token = $user->createToken('token', ['permission:user'])->plainTextToken;

                $userData = $this->service->returnAuthData(user: $user, token: $token);

                $this->status = 'success';
                $this->code = 200;
                $this->dataJson = $userData ;
                $this->message = 'Вход успешен';
            } else {
                $this->message = [
                    'password' => ['Пароль не совпадает'],
                ];
                $this->code = 400;
            }
        } else {
            $this->code = 400;
            $this->message = [
                'email' => ['Email не найден'],
            ];
        }

        return $this->responseJsonApi();
    }

}
