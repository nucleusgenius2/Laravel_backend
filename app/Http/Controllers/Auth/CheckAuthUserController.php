<?php

namespace App\Http\Controllers\Auth;

use App\DTO\DataArrayDto;
use App\Http\Controllers\Controller;
use App\Services\User\UserAuthDataService;
use App\Services\User\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckAuthUserController extends Controller
{
    protected UserAuthDataService $service;

    public function __construct(UserAuthDataService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request) : JsonResponse
    {
        $user = $request->user();

        $dataArrayDto = $this->service->authData(user: $user);

        if($dataArrayDto->status) {
            $this->status = 'success';
            $this->code = 200;
            $this->dataJson = $dataArrayDto->data;
        }
        else {
            $this->message = $dataArrayDto->error;
            $this->code = 400;
        }

        return $this->responseJsonApi();
    }
}
