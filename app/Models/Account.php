<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'fiat_coin',
        'type',
    ];
}
