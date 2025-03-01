<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Http\Requests\CryptoCloudRequest;
use App\Services\Payments\CryptoCloudWalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class CryptoCloudWalletController extends Controller
{
    protected CryptoCloudWalletService $service;

    public function __construct(CryptoCloudWalletService $service)
    {
        $this->service = $service;
    }

    /**
     * Возвращает сгенерированный номер кошелька crypto cloud для оплаты
     * @param CryptoCloudRequest $request
     * @return JsonResponse
     */
    public function createPayment(CryptoCloudRequest $request): JsonResponse
    {
        $data = $request->validated();

        $dataStringDto = $this->service->createInvoice(
            user: $request->user(),
            currency: $data['currency'],
            amount: $data['amount']
        );

        if ($dataStringDto->status){
            $this->status = 'success';
            $this->code = 200;
            $this->dataJson = $dataStringDto->data;
        }
        else{
            $this->message = $dataStringDto->error;
        }

        return $this->responseJsonApi();
    }

    public function callback(Request $request)
    {
        $dataEmptyDto = $this->service->collback(requestData: $request->all());

        if ($dataEmptyDto->status){
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
