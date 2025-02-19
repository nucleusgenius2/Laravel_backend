<?php

namespace App\Services\User;

use App\DTO\DataArrayDto;
use App\Events\UserLogin;
use App\Models\FsBalance;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginService
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function login(array $data, object $request)
    {
        $user = User::select('users.*', 'user_params.*','fiat_coin.code as main_currency')
            ->join('user_params', 'users.id', '=', 'user_params.id')
            ->join('fiat_coin', 'fiat_coin.id', '=', 'user_params.currency_id')
            ->where('users.email', $data['email'])
            ->first();

        if ($user) {
            if (Hash::check($data['password'], $user->password)) {
                $token = $user->createToken('token', ['permission:user'])->plainTextToken;

                $userData = $this->service->returnAuthData(user: $user, token: $token);

                event(new UserLogin(user: $user, request: $request));

                return new DataArrayDto(status: true, data: $userData );
            }
            else{
                $dataError = [
                    'password' => ['Пароль не совпадает'],
                ];

                return new DataArrayDto(status: false, data: $dataError, code: 400);
            }
        }
        else{
            $dataError = [
                'email' => ['Email не найден'],
            ];

            return new DataArrayDto(status: false, data: $dataError, code: 400);
        }
    }


}
