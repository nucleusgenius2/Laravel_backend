<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FiatCoin extends Model
{
    protected $table = 'fiat_coin';

    protected $fillable = [
        'img',
        'code',
        'name',
        'country',
        'type'
    ];
}
