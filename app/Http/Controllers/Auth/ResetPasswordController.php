<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Services\User\ResetPasswordService;
use Illuminate\Http\JsonResponse;

class ResetPasswordController extends Controller
{
    protected ResetPasswordService $service;

    public function __construct(ResetPasswordService $service)
    {
        $this->service = $service;
    }

    /**
     * Генерация кода восстановления пароля и отправка его на почту
     * @param EmailRequest $request
     * @return JsonResponse
     */
    public function resetGeneratedCode(EmailRequest $request): JsonResponse
    {
        $data = $request->validated();

        $dataEmptyDto = $this->service->getCodeResetPassword(email: $data['email'] );

        if( $dataEmptyDto->status){
            $this->status = 'success';
            $this->code = 200;
        }
        else {
            $this->code = $dataEmptyDto->code ?? 400;
            $this->message = $dataEmptyDto->error;
        }

        return $this->responseJsonApi();
    }


    /**
     * Установка нового пароля по коду
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $data = $request->validated();

        $dataEmptyDto = $this->service->resetPassword(password: $data['password'], code: $data['code']);
        if( $dataEmptyDto->status){
            $this->status = 'success';
            $this->code = 200;
        }
        else {
            $this->code = $dataEmptyDto->code ?? 400;
            $this->message = $dataEmptyDto->error;
        }

        return $this->responseJsonApi();
    }
}
