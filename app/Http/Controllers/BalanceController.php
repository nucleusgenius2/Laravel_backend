<?php

namespace App\Http\Controllers;

use App\Http\Requests\BalanceRequest;
use App\Models\Account;
use App\Services\BalanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;


class BalanceController extends Controller
{
    protected BalanceService $service;

    public function __construct(BalanceService $service){
        $this->service = $service;
    }


    public function index(): JsonResponse
    {
        $balance = $this->service->getBalance();

        $this->status = 'success';
        $this->code = 200;
        $this->dataJson = $balance;

        return $this->responseJsonApi();
    }

    public function store(BalanceRequest $request): JsonResponse
    {
        $data = $request->validated();

        $balance = $this->service->addBalance($data);

        if($balance['status']){
            $this->status = 'success';
            $this->code = 200;
        }
        else{
            $this->code = 500;
            $this->message = $balance['error'];
        }

        return $this->responseJsonApi();
    }

    public function setDefault(BalanceRequest $request, BalanceService $service): JsonResponse
    {
        $data = $request->validated();

        $balance = $service->setDefault($data);

        if($balance['status']){
            $this->status = 'success';
            $this->code = 200;
        }
        else{
            $this->code = 500;
            $this->message = $balance['error'];
        }

        return $this->responseJsonApi();
    }


}
