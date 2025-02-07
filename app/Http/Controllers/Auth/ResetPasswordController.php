<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\LimitResetPassword;
use App\Services\ResetPasswordService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    protected ResetPasswordService $service;

    public function __construct(ResetPasswordService $service)
    {
        $this->service = $service;
    }
    public function resetEmailMessage(EmailRequest $request): JsonResponse
    {
        $data = $request->validated();

        $returnData = $this->service->reset(email: $data['email'] );

        if($returnData->status){
            $this->status = 'success';
            $this->code = 200;
        }
        else {
            $this->message = $returnData->error;
        }

        return $this->responseJsonApi();
    }


    public function resetLink(ResetPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user, $password) {
                    $user->forceFill([
                        //'password' => Hash::make($password)
                        //  'password' => bcrypt($password)
                        'password' => $password
                    ])->setRememberToken(Str::random(60));
                    $user->save();

                    $this->status = 'success';

                    event(new PasswordReset($user));
                }
        );


        return $this->responseJsonApi();
    }
}
