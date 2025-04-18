<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LimitResetPassword extends Model
{
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'user_email',
        'code'
    ];
}

