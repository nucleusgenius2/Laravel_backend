<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chats extends Model
{
    public $timestamps = false;

    protected $casts = [
        'moderator' => 'boolean',
    ];

    protected $fillable = [
        'id',
        'user',
        'content',
        'created_at',
    ];
}
