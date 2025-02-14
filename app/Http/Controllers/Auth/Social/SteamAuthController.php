<?php

namespace App\Http\Controllers\Auth\Social;
use App\Http\Requests\AuthProviderRequest;
use App\Services\User\UserService;
use App\Traits\StructuredResponse;
use Laravel\Socialite\Facades\Socialite;

class SteamAuthController
{
    use StructuredResponse;

    public UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function redirect(AuthProviderRequest $request)
    {
        $data = $request->validated();

        $sessionData = [
            'user_auth.currency' => $data['currency']
        ];

        if (isset($data['refCode'])){
            $sessionData['user_auth.ref'] = $data['refCode'];
        }

        session($sessionData);

        return Socialite::driver('steam')->redirect();
    }

    public function handleCallback()
    {
        $user = Socialite::driver('steam')->stateless()->user();

        $data = $this->service->authSocial($user, 'Steam');

        if ($data['status']) {
            $this->status = 'success';
            $this->code = 200;
            $this->dataJson = $data['returnData'] ;
            $this->message = 'Вход успешен';
        }
        else{
            $this->code = 500;
            $this->status = 'error';
            $this->message = $data['error'];
        }

        return $this->responseJsonApi();
    }

}
