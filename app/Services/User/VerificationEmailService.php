<?php

namespace App\Services\User;

use App\DTO\DataEmptyDto;
use App\Mail\VerificationEmail;
use App\Models\ConfigWinmove;
use App\Models\EmailVerifications;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class VerificationEmailService
{
    public function getCodeVerificationEmail(User $user, string $email): DataEmptyDto
    {
        $code = mt_rand(10000, 99999);

        $limit = ConfigWinmove::where('param', 'change_email_limit')->first();
        if($limit->is_active) {
            $verifications = EmailVerifications::where('user_id', $user->id)->first();

            if ($verifications) {
                if($verifications->count < $limit->val){
                    $verifications->email = $email;
                    $verifications->save();

                    $this->sendMessageEmailVerification($code, email: $email);

                    return new DataEmptyDto(status: true);
                }
                else{
                    return new DataEmptyDto(status: false, error: 'Лимит смены почт достигнут');
                }
            }
            else {
                EmailVerifications::create([
                    'user_id' => $user->id,
                    'count' => 1,
                    'code' => $code,
                    'email' => $email,
                ]);

                $this->sendMessageEmailVerification(code: $code, email: $email);

                return new DataEmptyDto(status: true);
            }
        }
        return new DataEmptyDto(status: false, error: 'Опция отключена');
    }

    public function sendMessageEmailVerification(string $code, string $email): void
    {
        Mail::to($email)->send(new VerificationEmail($code, $email));
    }

    public function verificationEmail(User $user, string $code): DataEmptyDto
    {
        $verifications = EmailVerifications::where([['code', $code],['user_id', $user->id]])->first();
        if($verifications) {
            try {
                $verifications->count++;
                $user->save();

                $user->email = $verifications->email;
                $user->save();

                return new DataEmptyDto(status: true);
            } catch (\Exception $e) {
                return new DataEmptyDto(status: false, error: $e, code: 500);
            }
        }
        else{
            return new DataEmptyDto(status: false, error: 'Код не верен', code: 400);
        }
    }
}
