<?php

namespace App\Http\Controllers;

use App\DTO\DataEmptyDto;
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

    public function index_fs(Request $request): JsonResponse
    {
        $dataEmptyDto = $this->service->getBalanceFs(user: $request->user());

        $this->status = 'success';
        $this->code = 200;
        $this->dataJson = $dataEmptyDto->data;

        return $this->responseJsonApi();
    }


    /**
     * Добавление баланса юзеру
     * @param BalanceRequest $request
     * @return JsonResponse
     */
    public function store(BalanceRequest $request): JsonResponse
    {
        $data = $request->validated();

        $dataEmptyDto = $this->service->addBalance(currency: $data['currency'], user: $request->user());

        if($dataEmptyDto->status){
            $this->status = 'success';
            $this->code = 200;
        }
        else{
            $this->code = $dataEmptyDto->code;
            $this->message = $dataEmptyDto->error;
        }

        return $this->responseJsonApi();
    }

    /**
     * Выбор главного баланса юзера (при выборе конвертирует бонусный счет в выбранную валюту)
     * @param BalanceRequest $request
     * @return JsonResponse
     */
    public function setDefault(BalanceRequest $request): JsonResponse
    {
        $data = $request->validated();

        $dataEmptyDto = $this->service->setDefault(currency: $data['currency'], user: $request->user());

        if($dataEmptyDto->status){
            $this->status = 'success';
            $this->code = 200;
        }
        else{
            $this->code = $dataEmptyDto->code;
            $this->message = $dataEmptyDto->error;
        }

        return $this->responseJsonApi();
    }


}
