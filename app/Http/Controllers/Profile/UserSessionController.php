<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\PageRequest;
use App\Services\User\UserSessionService;

class UserSessionController extends Controller
{
    public int $perPageFrontend = 10;

    protected UserSessionService $service;

    public function __construct(UserSessionService $service)
    {
        $this->service = $service;
    }

    public function index(PageRequest $request)
    {
        $data = $request->validated();

        $dataObjectDto = $this->service->getHistory(user: $request->user(), page: $data['page'], perPage: $this->perPageFrontend );

        $this->status = 'success';
        $this->code = 200;
        $this->dataJson = $dataObjectDto->data;

        return $this->responseJsonApi();
    }
}


