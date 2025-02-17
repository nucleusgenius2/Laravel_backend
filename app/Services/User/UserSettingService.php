<?php

namespace App\Services\User;

use App\DTO\DataEmptyDto;
use App\Models\User;

class UserSettingService
{
    public function saveSetting(User $user, array $data): DataEmptyDto
    {
        if( empty($data) ){
            return new DataEmptyDto(status: false, error: 'данные отсутствуют', code: 422);
        }

        $userParams = $user->params()->first();

        if(isset($data['cfg_hidden_name'])){
            $userParams->cfg_hidden_name = $data['cfg_hidden_name'];
        }
        if(isset($data['cfg_hidden_stat'])){
            $userParams->cfg_hidden_stat = $data['cfg_hidden_stat'];
        }
        $userParams->save();

        return new DataEmptyDto(status: true);

    }
}
