<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Http\Requests\PaginationRequest;
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

    /**
     * Возвращает историю пополнений баланса для юзера
     * @param PaginationRequest $request
     * @return JsonResponse
     */
    public function index(PaginationRequest $request): JsonResponse
    {
        $data = $request->validated();

        $dataEmptyDto = $this->service->getPayments(user: $request->user(), data: $data);

        $this->status = 'success';
        $this->dataJson = $dataEmptyDto->data;
        $this->code = 200;

        return $this->responseJsonApi();
    }
}
