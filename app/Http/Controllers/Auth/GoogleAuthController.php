<?php

namespace App\Http\Controllers\Auth;
use App\Http\Requests\AuthProviderRequest;
use App\Models\User;
use App\Services\UserService;
use App\Traits\StructuredResponse;

use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController
{
    use StructuredResponse;

    public UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function redirectToGoogle(AuthProviderRequest $request)
    {
        $data = $request->validated();

        $sessionData = [
            'user_auth.currency' => $data['currency']
        ];

        if (isset($data['refCodey'])){
            $sessionData['user_auth.ref'] = $data['refCodey'];
        }

        session($sessionData);

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->user();

        $data = $this->service->authSocial($googleUser);

        if ($data['status']) {
            $this->status = 'success';
            $this->code = 200;
            $this->dataJson = $data['data'] ;
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
