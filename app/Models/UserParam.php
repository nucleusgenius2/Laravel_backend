<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserParam extends Model
{
    protected $fillable = [
        'avatar',
        'level',
        'gameCount',
        'referal',
        'currency',
    ];
}
