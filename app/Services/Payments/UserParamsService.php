<?php

namespace App\Services\Payments;

use App\DTO\DataArrayDto;
use App\Models\User;

class UserParamsService
{
    public function getParams(User $user): DataArrayDto
    {

        $data = [
            'email' => [
                'status' => (bool)$user->email,
                'value' => $user->email ?? null,
            ],
            'password' => [
                'status' => (bool)$user->password,
            ],
            '2fa' => [
                'status' => false,
            ]
        ];

        return new DataArrayDto(status: true, data: $data );
    }

}
