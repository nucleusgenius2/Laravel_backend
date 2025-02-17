<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'browser',
        'region',
        'ip',
        'date',
        'user_id',
    ];
}
