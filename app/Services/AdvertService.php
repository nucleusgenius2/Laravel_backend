<?php

namespace App\Services;

use App\DTO\DataObjectDto;
use App\Models\Advert;

class AdvertService
{
    public function getAdverts(int $count){
        $adverts = Advert::where('status', true)->limit($count)->get();

        return new DataObjectDto(status: true, data:  $adverts);
    }
}
