<?php

namespace App\Services;

use App\DTO\DataStringDto;
use App\Models\LimitResetPassword;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

class ResetPasswordService
{
     public function reset(string $email): DataStringDto
     {
         $user = User::find('email', $email)->first();

         $limit = LimitResetPassword::where('user_email', '=', $user->email)->first();

         if ( $limit ) {
             if ($limit->updated_at > Carbon::today()) {
                 return new DataStringDto(status: false, error: 'Восстановление пароля 1 раз в час');
             }
             else{
                 $this->sendMessageResetPassword($user->email);
             }
         }
         else{
             LimitResetPassword::create([
                 'user_email' => $user->email
             ]);

             $this->sendMessageResetPassword($user->email);

             return new DataStringDto(status: true);
         }
     }

    public function sendMessageResetPassword(string $email){
        $status = Password::sendResetLink(
            ['email' => $email]
        );
        log::info($status);
    }
}
