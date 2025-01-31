<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;



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

        $user = User::select('users.*', 'user_params.level', 'user_params.currency', 'user_params.referal')
            ->join('user_params', 'users.id', '=', 'user_params.id')
            ->where('users.email', $data['email'])
            ->first();

        if ($user) {
            if (Hash::check($data['password'], $user->password)) {
                $token = $user->createToken('token', ['permission:user'])->plainTextToken;

                $userData = [
                    'token' => $token,
                    'user' => $user->name,
                    'level' => $this->service->getUserLevel($user->id),
                    'currency' => $user->currency,
                    'balance' => 0
                ];

                $this->status = 'success';
                $this->code = 200;
                $this->dataJson = $userData ;
                $this->message = 'Вход успешен';
            } else {
                $this->message = 'Пароль не совпадает';
                $this->code = 401;
            }
        } else {
            $this->code = 404;
            $this->message = 'Email не найден';
        }

        return $this->responseJsonApi();
    }

}
