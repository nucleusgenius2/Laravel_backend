<?php

namespace App\Http\Controllers\State;

use App\Http\Controllers\Controller;
use App\Http\Requests\CountRequest;
use App\Models\Advert;
use Illuminate\Http\JsonResponse;

class AdvertController extends Controller
{
    public function index(CountRequest $request): JsonResponse
    {
        $data = $request->validated();

        $advert = Advert::where('status', true)->limit($data['count'])->get();

        $this->status = 'success';
        $this->code = 200;
        $this->dataJson = $advert;

        return $this->responseJsonApi();
    }
}
