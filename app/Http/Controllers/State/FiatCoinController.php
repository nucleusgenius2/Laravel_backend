<?php

namespace App\Http\Controllers\State;

use App\Http\Controllers\Controller;
use App\Models\FiatCoin;
use Illuminate\Http\JsonResponse;


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
}
