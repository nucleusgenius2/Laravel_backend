<?php

namespace App\Http\Controllers\State;

use App\DTO\DataArrayDto;
use App\DTO\DataObjectDto;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Countries;
use App\Models\FiatCoin;
use App\Models\UserParam;
use App\Services\ChatService;
use App\Services\UserFiatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class FiatCoinController extends Controller
{

    protected UserFiatService $service;

    public function __construct(UserFiatService $service){
        $this->service = $service;
    }

     public function index(): JsonResponse
     {
         $dataObjectDto = $this->service->getCurrencies();

         if($dataObjectDto->status) {
             $this->status = 'success';
             $this->code = 200;
             $this->dataJson = $dataObjectDto->data;
         }

         return $this->responseJsonApi();
     }

    public function show(string $code): JsonResponse
    {
        $dataObjectDto = $this->service->showCurrencies(code: $code);

        if ($dataObjectDto->status) {
            $this->status = 'success';
            $this->code = 200;
            $this->dataJson = $dataObjectDto->data;
        } else {
            $this->code = 404;
            $this->message = $dataObjectDto->error;
        }

        return $this->responseJsonApi();
    }

    public function getFiatUser(): JsonResponse
    {
        $user = Auth::user();

        $dataObjectDto = $this->service->getUserCurrenciesFullData(user: $user);

        if( $dataObjectDto->status){
            $this->status = 'success';
            $this->code = 200;
            $this->dataJson = $dataObjectDto->data;
        } else{
            $this->code = 500;
        }

        return $this->responseJsonApi();
    }


}
