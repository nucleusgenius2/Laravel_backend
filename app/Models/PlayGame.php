<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayGame extends Model
{
    protected $table = 'play_game';

    public $timestamps = false;

    protected $fillable = [
        'gameid',
        'date_play',
        'bet',
        'win',
        'ratio',
        'user',
    ];
}
