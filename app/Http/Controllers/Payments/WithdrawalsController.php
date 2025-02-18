<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Services\Payments\WithdrawalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WithdrawalsController extends Controller
{
    protected WithdrawalService $service;

    public function __construct(WithdrawalService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        $dataEmptyDto = $this->service->getPayments(user: $request->user());

        if( $dataEmptyDto->status){
            $this->status = 'success';
            $this->dataJson = $dataEmptyDto->data;
            $this->code = 200;
        }
        else {
            $this->code = 400;
        }

        return $this->responseJsonApi();
    }
}
