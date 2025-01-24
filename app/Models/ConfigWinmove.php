<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigWinmove extends Model
{
    protected $table = 'config_winmove';

    public $timestamps = false;

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $fillable = [
        'param',
        'is_active',
        'val',
    ];
}
