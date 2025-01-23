<?php

namespace App\Http\Controllers\Auth\Social;
use App\Http\Requests\AuthProviderRequest;
use App\Services\UserService;
use App\Traits\StructuredResponse;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class VkAuthController
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

        return Socialite::driver('vkontakte')->stateless()->redirect();
    }

    public function handleCallback()
    {
        $user = Socialite::driver('vkontakte')->stateless()->user();

        $data = $this->service->authSocial($user, 'VK');

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
