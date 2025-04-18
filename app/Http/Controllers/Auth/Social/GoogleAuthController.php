<?php

namespace App\Http\Controllers\Auth\Social;
use App\Http\Requests\AuthProviderRequest;
use App\Services\User\UserService;
use App\Traits\StructuredResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController
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

        return Socialite::driver('google')->redirect();
    }

    public function handleCallback(Request $request)
    {
        //$user = Socialite::driver('google')->user();
        $user = Socialite::driver('google')->userFromToken($request->code);

        $optionalDate =[
            $request->header('currency'),
            $request->header('refCode') ?? false,
        ];

        $data = $this->service->authSocial($user, 'Google', $optionalDate);

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
