<?php

namespace App\Http\Controllers;

use App\Http\Requests\BalanceRequest;
use App\Models\Account;
use App\Services\BalanceService;
use App\Services\UserFiatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class BalanceController extends Controller
{
    protected BalanceService $service;

    public function __construct(BalanceService $service){
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $balance = $this->service->getBalance(user: $request->user());

        $this->status = 'success';
        $this->code = 200;
        $this->dataJson = $balance;

        return $this->responseJsonApi();
    }

    public function store(BalanceRequest $request): JsonResponse
    {
        $data = $request->validated();

        $dataEmptyDto = $this->service->addBalance(currency: $data['currency'], user: $request->user());

        if($dataEmptyDto->status){
            $this->status = 'success';
            $this->code = 200;
        }
        else{
            $this->code = 400;
            $this->message = $dataEmptyDto->error;
        }

        return $this->responseJsonApi();
    }

    public function setDefault(BalanceRequest $request): JsonResponse
    {
        $data = $request->validated();

        $dataEmptyDto = $this->service->setDefault(currency: $data['currency'], user: $request->user());

        if($dataEmptyDto->status){
            $this->status = 'success';
            $this->code = 200;
        }
        else{
            $this->code = 500;
            $this->message = $dataEmptyDto->error;
        }

        return $this->responseJsonApi();
    }


}
