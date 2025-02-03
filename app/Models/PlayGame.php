<?php

namespace App\Models;

use App\DTO\WebsocketDto;
use App\Events\NotificationsWebsocketSend;
use App\Events\WinnersWebsocketSend;
use App\Services\GameService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlayGame extends Model
{
    protected $table = 'play_game';

    public $timestamps = false;

    protected $fillable = [
        'gameId',
        'date_play',
        'bet',
        'win',
        'ratio',
        'user_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($playGame) {
            if ($playGame->win > 0) {
                DB::afterCommit(function () use ($playGame) {
                    $service = app()->make(GameService::class);
                    $data = $service->getPlayGame($playGame->id);

                    $WebsocketDto = new WebsocketDto($playGame->user_id, $data['user']['name'], json_encode($data));
                    event(new WinnersWebsocketSend($WebsocketDto));
                });
            }
        });
    }
}
