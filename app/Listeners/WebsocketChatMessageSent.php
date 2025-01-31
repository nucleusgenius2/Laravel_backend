<?php

namespace App\Listeners;

use App\Events\ChatMessageSent;
use App\Events\NotificationsWebsocketSend;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebsocketChatMessageSent
{
    public function handle(NotificationsWebsocketSend|ChatMessageSent $event): void
    {
        $url = config("websocket.websocket_node_local_uri");

        $data = [
            'type' => $event->getType(),
            'data' => $event->getData(),
            'user_name' => $event->getUserName(),
            'user_id' => $event->getUserId()
        ];
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
