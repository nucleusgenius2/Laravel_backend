<?php

namespace App\Http\Controllers\Auth\Social;
use App\Http\Requests\AuthProviderRequest;
use App\Services\UserService;
use App\Traits\StructuredResponse;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class TelegramAuthController
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

        if (isset($data['refCodey'])){
            $sessionData['user_auth.ref'] = $data['refCodey'];
        }

        session($sessionData);

        return Socialite::driver('telegram')->redirect();
    }

    public function handleCallback()
    {
        $user = Socialite::driver('telegram')->user();

        $data = $this->service->authSocial($user, 'Telegram');

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
