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
        if(isset($data['cfg_sound'])){
            $userParams->cfg_sound = $data['cfg_sound'];
        }
        if(isset($data['cfg_music'])){
            $userParams->cfg_music = $data['cfg_music'];
        }
        if(isset($data['cfg_effect'])){
            $userParams->cfg_effect = $data['cfg_effect'];
        }
        if(isset($data['cfg_hidden_game'])){
            $userParams->cfg_hidden_game = $data['cfg_hidden_game'];
        }
        if(isset($data['cfg_animation'])){
            $userParams->cfg_animation = $data['cfg_animation'];
        }

        $userParams->save();

        return new DataEmptyDto(status: true);

    }
}
