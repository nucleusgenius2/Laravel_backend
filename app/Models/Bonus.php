<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    protected $table = 'bonus';

    protected $fillable = [
        'name',
        'type',
        'amount',
        'valid',
        'bonus_type',
        'bonus_amount'
    ];
}
