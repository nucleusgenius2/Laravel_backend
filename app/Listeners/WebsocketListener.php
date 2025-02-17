<?php

namespace App\Listeners;

use App\Events\ChatMessageWebsocketSend;
use App\Events\NotificationsWebsocketSend;
use App\Events\WinnersWebsocketSend;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebsocketListener
{
    public function handle(NotificationsWebsocketSend|WinnersWebsocketSend|ChatMessageWebsocketSend $event): void
    {
        $url = config("websocket.websocket_node_local_uri");

        $data = [
            'type' => $event->getType(),
            'data' => $event->getData(),
            'user_id' => $event->getUserId(),
            'user' => [
                'name' => $event->getUserName(),
            ],
        ];

        if (method_exists($event, 'getUserDetails') ) {
            $userDetails = $event->getUserDetails();
            $data['user']['avatar'] = $userDetails->avatar;
            $data['user']['level'] = $userDetails->level;
        }


        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($url, $data);
        }
        catch (\Exception $e) {
           Log::error('Ошибка соединения с сервером вебсокетов на node', ['message' => $e->getMessage()]);
        }
    }
}
