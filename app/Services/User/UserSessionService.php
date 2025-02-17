<?php

namespace App\Services\User;

use App\DTO\DataObjectDto;
use App\Models\User;
use App\Models\UserSession;

class UserSessionService
{

    public function getHistory(User $user, int $page, int $perPage): DataObjectDto
    {
        $session = UserSession::where('user_id', $user->id)->paginate($perPage, ['*'], 'page', $page);

        return new DataObjectDto(status: true, data: $session);
    }
}
