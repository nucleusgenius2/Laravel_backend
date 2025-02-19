<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Http\Requests\CryptoCloudRequest;
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

    public function createPayment(Request $request): JsonResponse
    {
        //$data = $request->validated();

        $dataStringDto = $this->service->createInvoice(user: $request->user());

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
       // $this->service->collback(requestData: $request->all());

        log::info('колбек exnode');
        log::info($request->all());

        //$this->code = 200;
        //return response()->json();
    }

    public function getTokens(Request $request): JsonResponse
    {
        $dataStringDto = $this->service->testToken();

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
}
