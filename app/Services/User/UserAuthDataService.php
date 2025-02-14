<?php

namespace App\Services\User;



use App\DTO\DataArrayDto;
use App\Models\FiatCoin;
use App\Models\User;
use App\Models\UserParam;

class UserAuthDataService
{
    protected UserService $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    public function authData(User $user): DataArrayDto
    {
        try {
            $userParams = UserParam::select('fiat_coin.code as code')
                ->where('user_params.id', $user->id)
                ->join('fiat_coin', 'fiat_coin.id', '=', 'user_params.currency_id')
                ->first();


            $data = [
                'level' => $this->service->getUserLevel(userId: $user->id),
                'balance' => $this->service->getMainBalance($user->id),
                'main_currency' => $userParams->code,
            ];

            return new DataArrayDto(status: true, data: $data);
        } catch (\Exception $e) {
            return new DataArrayDto(status: false, error: $e);
        }

    }
}
