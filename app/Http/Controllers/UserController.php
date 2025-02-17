<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\User\UserService;
use App\Traits\UploadsImages;

class UserController extends Controller
{
    protected UserService $service;

    public function __construct(UserService $service){
        $this->service = $service;
    }

    public function store(UserRequest $request, User $user)
    {
        $data = $request->validated();

        $dataEmptyDto = $this->service->updateUser(data: $data, user: $request->user());

        if ($dataEmptyDto->status) {
            $this->status = 'success';
            $this->code = 200;
        } else {
            $this->code = $dataEmptyDto->code ?? 400;
            $this->message = $dataEmptyDto->error;
        }

        return $this->responseJsonApi();
    }

}
