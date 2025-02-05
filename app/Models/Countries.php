<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Countries extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $fillable = [
        'code',
        'currencies',
        'title',
        'phone_prefix'
    ];
}
