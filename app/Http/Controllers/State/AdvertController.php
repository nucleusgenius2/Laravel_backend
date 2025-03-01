<?php

namespace App\Http\Controllers\State;

use App\Http\Controllers\Controller;
use App\Http\Requests\CountRequest;
use App\Models\Advert;
use App\Services\AdvertService;
use App\Services\UserFiatService;
use Illuminate\Http\JsonResponse;

class AdvertController extends Controller
{
    protected AdvertService $service;

    public function __construct(AdvertService $service){
        $this->service = $service;
    }

    public function index(CountRequest $request): JsonResponse
    {
        $data = $request->validated();

        $dataObjectDto = $this->service->getAdverts(count: $data['count']);

        $this->status = 'success';
        $this->code = 200;
        $this->dataJson = $dataObjectDto->data;

        return $this->responseJsonApi();
    }
}
