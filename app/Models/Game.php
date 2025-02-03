<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'gameId',
        'type',
        'img',
        'title',
    ];
}
