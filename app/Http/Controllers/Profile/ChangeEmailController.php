<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailRequest;
use App\Http\Requests\RequestVerificationEmail;
use App\Services\User\VerificationEmailService;
use Illuminate\Http\JsonResponse;

class ChangeEmailController extends Controller
{
    protected VerificationEmailService $service;

    public function __construct(VerificationEmailService $service)
    {
        $this->service = $service;
    }

    public function changeEmail(EmailRequest $request){

        $data = $request->validated();

        $dataEmptyDto = $this->service->getCodeVerificationEmail(user: $request->user(), email: $data['email']);

        if( $dataEmptyDto->status){
            $this->status = 'success';
            $this->code = 200;
        }
        else {
            $this->code = 400;
            $this->message = $dataEmptyDto->error;
        }

        return $this->responseJsonApi();
    }

    public function verificationEmail(RequestVerificationEmail $request): JsonResponse
    {
        $data = $request->validated();

        $dataEmptyDto = $this->service->verificationEmail(user: $request->user(), code: $data['code']);
        if($dataEmptyDto->status){
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
