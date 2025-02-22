<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayMethod  extends Model
{
    protected $fillable = [
        'code',
        'name',
        'network',
        'processing',
        'status',
        'countries',
        'type',
    ];
}
