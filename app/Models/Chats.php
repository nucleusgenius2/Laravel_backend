<?php

namespace App\Models;

use App\DTO\WebsocketDto;
use App\Events\ChatMessageSent;
use App\Events\NotificationsWebsocketSend;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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


    protected static function boot()
    {
        parent::boot();

        static::created(function ($chatMessage) {

            $WebsocketDto = new WebsocketDto(userId: $chatMessage->user, userName: Auth::user()->name,  data: $chatMessage->content);

            event(new ChatMessageSent($WebsocketDto));
        });
    }
}
