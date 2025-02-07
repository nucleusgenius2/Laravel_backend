<?php

namespace App\Http\Controllers;

use App\Models\Countries;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function setCountry(Request $request): JsonResponse
    {

        $position = Location::get($request->ip());
        $response= array(
            "code" => $position->countryCode,
            "phone_prefix" => Countries::where('code',$position->countryCode)->value('phone_prefix'),
            "title" => $position->countryName,
            "currency" => $position->currencyCode
        );
        $this->status = 'success';
        $this->code = 200;
        $this->dataJson = $response;

        return $this->responseJsonApi();
    }
}
