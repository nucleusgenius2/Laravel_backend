<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FsBalance extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'count',
        'nominal',
        'to_date',
        'user_id',
        'type'
    ];
}

