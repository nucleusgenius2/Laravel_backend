<?php

namespace App\Services;

use App\DTO\DataArrayDto;
use App\DTO\DataEmptyDto;
use App\DTO\DataObjectDto;
use App\Models\Countries;
use Stevebauman\Location\Facades\Location;

class CountryService
{
    public function getCountry(): DataObjectDto
    {
        $countries = Countries::select('code','title','phone_prefix')->get();

        return new DataObjectDto(status: true, data: $countries );
    }

    public function setCountry(string $ip): DataArrayDto
    {
        $position = Location::get($ip);
        try {
            $data = array(
                "code" => $position->countryCode,
                "phone_prefix" => Countries::where('code',$position->countryCode)->value('phone_prefix'),
                "title" => $position->countryName,
                "currency" => $position->currencyCode
            );
            return new DataArrayDto(status: true, data: $data);
        }
        catch (\Exception $e){
            return new DataArrayDto(status: false, error: 'Код вашей старны не найден', code: 400);
        }

    }

}
