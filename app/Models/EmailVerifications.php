<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailVerifications extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'user_id',
        'code',
        'count',
        'email'
    ];
}
