<?php

namespace App\Http\Controllers;

use App\DTO\DataObjectDto;
use App\Models\Countries;
use App\Services\CountryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stevebauman\Location\Facades\Location;

class CountryController extends Controller
{
    protected CountryService$service;

    public function __construct(CountryService $service){
        $this->service = $service;
    }

    public function index(): JsonResponse
    {
        $dataObjectDto = $this->service->getCountry();

        if($dataObjectDto->status) {
            $this->dataJson = $dataObjectDto->data;
            $this->status = 'success';
            $this->code = 200;
        }

        return $this->responseJsonApi();
    }

    public function setCountry(Request $request): JsonResponse
    {
        $dataArrayDto = $this->service->setCountry(ip: $request->ip());

        if($dataArrayDto->status) {
            $this->status = 'success';
            $this->code = 200;
            $this->dataJson = $dataArrayDto->data;
        }
        else{
            $this->code = 400;
            $this->dataJson = $dataArrayDto->error;
        }

        return $this->responseJsonApi();
    }
}
