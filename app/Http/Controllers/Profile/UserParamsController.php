<?php

namespace App\Http\Controllers\Profile;

use App\DTO\DataArrayDto;
use App\Http\Controllers\Controller;
use App\Services\Payments\UserParamsService;
use App\Services\User\UserSessionService;
use Illuminate\Http\Request;


class UserParamsController extends Controller
{
    protected UserParamsService $service;

    public function __construct(UserParamsService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $dataArrayDto = $this->service->getParams($request->user());

        $this->status = 'success';
        $this->code = 200;
        $this->dataJson = $dataArrayDto->data;

        return $this->responseJsonApi();

    }
}
