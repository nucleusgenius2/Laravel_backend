<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserParam extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'avatar',
        'level',
        'gameCount',
        'referal',
        'currency',
        'refer_id'
    ];
}
