<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class CountryController extends Controller
{
    public function setCountry(): JsonResponse
    {
        $this->status = 'success';
        $this->code = 200;

        return $this->responseJsonApi();
    }
}
