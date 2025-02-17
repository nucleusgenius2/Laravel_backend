<?php

namespace App\Listeners;

use App\Events\UserLogin;
use App\Models\UserSession;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UserLoginListener
{
    public function handle(UserLogin $event): void
    {
        $user = $event->getUser();

        $saveHistory = UserSession::create([
            'user_id' => $user->id,
            'browser' => $event->getBrowser(),
            'ip' => $event->getIp(),
            'region' => $event->getRegion(),
            'date' => Carbon::now()
        ]);

        if(!$saveHistory){
            log::error('Ошибка сохранения истории входов для юзера '.$user->id);
        }


    }
}
