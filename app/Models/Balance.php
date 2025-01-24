<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'amount',
        'to_date',
        'count',
        'nominal',
        'account_id'
    ];
}
