<?php

namespace App\Services\User;

use App\DTO\DataEmptyDto;
use App\Models\LimitResetPassword;
use App\Models\User;
use App\Notifications\ResetPassword;
use Illuminate\Support\Carbon;

class ResetPasswordService
{
     public function getCodeResetPassword(string $email): DataEmptyDto
     {
         $user = User::where('email', $email)->first();

         if(!$user){
             return new DataEmptyDto(status: false, error: 'Пользователь не найден');
         }

         $limit = LimitResetPassword::where('user_email', '=', $user->email)->first();

         if ( $limit ) {
             if ($limit->updated_at > Carbon::now()->subHour()) {
                 return new DataEmptyDto(status: false, error: 'Восстановление пароля 1 раз в час');
             }
             else{
                 $this->sendMessageResetPassword(user: $user);

                 return new DataEmptyDto(status: true);
             }
         }
         else{
             LimitResetPassword::create([
                 'user_email' => $user->email,
                 'code' => '',
             ]);

             $this->sendMessageResetPassword( user: $user);

             return new DataEmptyDto(status: true);
         }
     }

    public function sendMessageResetPassword(User $user): void
    {
        $code = mt_rand(10000, 99999);

        LimitResetPassword::where('user_email', $user->email)->update([
            'code' => $code,
        ]);

        $user->notify(new ResetPassword($code));
    }

    public function resetPassword(string $password, string $code): DataEmptyDto
    {
        $reset = LimitResetPassword::where('code', $code)->first();
        if($reset){
            $user = User::where('email', $reset->user_email)->first();
            $user->password = $password;
            $user->save();

            $reset->code = '';
            $reset->save();

            return new DataEmptyDto(status: true);
        }
        else{
            return new DataEmptyDto(status: false, error: 'Юзер не найден');
        }
    }

}
