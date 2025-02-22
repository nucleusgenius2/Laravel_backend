<?php

namespace App\Http\Controllers\Payments;

use App\DTO\DataObjectDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\CountryRequest;
use App\Http\Requests\RequestPaymentCrypto;
use App\Services\Payments\CryptoPaymentService;
use Illuminate\Http\JsonResponse;

class CryptoPaymentsController extends Controller
{
    protected CryptoPaymentService $service;

    public function __construct(CryptoPaymentService $service)
    {
        $this->service = $service;
    }

    public function index(CountryRequest $request)
    {
        $data = $request->validated();

        $dataObjectDto = $this->service->getMethods(country: $data['country']);

        if ( $dataObjectDto->status){
            $this->status = 'success';
            $this->code = 200;
            $this->dataJson = $dataObjectDto->data;
        }
        else{
            $this->message = $dataObjectDto->error;
        }

        return $this->responseJsonApi();
    }
}
