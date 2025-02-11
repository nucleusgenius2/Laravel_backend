<?php

namespace App\Models;

use App\DTO\WebsocketDto;
use App\Events\NotificationsWebsocketSend;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id',
        'user_id',
        'content',
        'read',
        'created_ad',
    ];

    protected $casts = [
        'read' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($notification) {
            $WebsocketDto = new WebsocketDto(userId: $notification->user_id,  userName: 'empty',  data: $notification->content);

            event(new NotificationsWebsocketSend($WebsocketDto));
        });
    }


}
