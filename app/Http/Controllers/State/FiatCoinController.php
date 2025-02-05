<?php

namespace App\Http\Controllers\State;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Countries;
use App\Models\FiatCoin;
use App\Models\UserParam;
use App\Services\UserFiatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class FiatCoinController extends Controller
{
     public function index(): JsonResponse
     {
         $this->status = 'success';
         $this->code = 200;
         $this->dataJson = FiatCoin::all();

         return $this->responseJsonApi();
     }

    public function show(string $code): JsonResponse
    {
        if (!ctype_alnum($code)) {
            $this->message = 'Не валидные данные';
        } else {
            $currency = FiatCoin::where('code', $code)->first();

            if ($currency) {
                $this->status = 'success';
                $this->code = 200;
                $this->dataJson = $currency;
            } else {
                $this->code = 404;
                $this->message = 'Валюта не найдена';
            }
        }

        return $this->responseJsonApi();
    }

    public function getFiatUser(UserFiatService $service): JsonResponse
    {
        $user = Auth::user();

        $data = $service->getUserCurrencies(user: $user);
        if($data !== null){
            $this->status = 'success';
            $this->code = 200;
            $this->dataJson = $data;
        } else{
            $this->code = 500;
        }

        return $this->responseJsonApi();
    }


}
