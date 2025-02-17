<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserSettingRequest;
use App\Services\User\UserSettingService;

class UserSettingController extends Controller
{
    protected UserSettingService $service;

    public function __construct(UserSettingService $service)
    {
        $this->service = $service;
    }

    public function store(UserSettingRequest $request)
    {
        $data = $request->validated();

        $dataEmptyDto = $this->service->saveSetting(user: $request->user(), data: $data);

        if( $dataEmptyDto->status){
            $this->status = 'success';
            $this->code = 200;
        }
        else {
            $this->code = $dataEmptyDto->code;
            $this->message = $dataEmptyDto->error;
        }

        return $this->responseJsonApi();
    }
}
