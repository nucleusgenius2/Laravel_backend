<?php

namespace App\Http\Controllers;

use App\Models\Countries;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Stevebauman\Location\Facades\Location;

class CountryController extends Controller
{
    public function index(): JsonResponse
    {
        $this->status = 'success';
        $this->code = 200;
        $this->dataJson = Countries::select('code','title','phone_prefix')->get();
        return $this->responseJsonApi();
    }

    public function setCountry(): JsonResponse
    {
        $this->status = 'success';
        $this->code = 200;

        return $this->responseJsonApi();
    }
}
