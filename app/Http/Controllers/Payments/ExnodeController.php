<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Http\Requests\CryptoCloudRequest;
use App\Http\Requests\ExnodeRequest;
use App\Services\Payments\ExnodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ExnodeController extends Controller
{
    protected ExnodeService $service;

    public function __construct(ExnodeService $service)
    {
        $this->service = $service;
    }

    public function createPayment(ExnodeRequest $request): JsonResponse
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

        log::info('колбек exnode');

        log::info($request->all());

        //$this->code = 200;
        //return response()->json();

        if ($dataEmptyDto->status){
            $this->status = 'success';
            $this->code = 200;
            $this->dataJson = $dataEmptyDto->data;
        }
        else{
            $this->message = $dataEmptyDto->error;
        }

        return $this->responseJsonApi();
    }

    /**
     * Получение доступных токенов для exnode
     * @return JsonResponse
     */
    public function getTokens(): JsonResponse
    {
        $dataArrayDto = $this->service->getAvailableTokens();

        if ( $dataArrayDto->status){
            $this->status = 'success';
            $this->code = 200;
            $this->dataJson = $dataArrayDto->data;
        }
        else{
            $this->message = $dataArrayDto ->error;
        }

        return $this->responseJsonApi();
    }
}
