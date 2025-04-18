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
        'currency_id',
        'refer_id',
        'country',
        'cfg_music',
        'cfg_sound',
        'cfg_effect',
        'cfg_animation',
        'cfg_hidden_game'
    ];
}
