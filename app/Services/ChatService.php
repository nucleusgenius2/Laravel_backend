<?php

namespace App\Services;

use App\Models\Chats;
use Illuminate\Support\Collection;

class ChatService
{
    public function getDataChat(int $count): Collection
    {
        $messages = Chats::join('users', 'users.id', '=', 'chats.user')
            ->leftJoin('user_params', 'user_params.id', '=', 'users.id')
            ->select('chats.*', 'users.name', 'user_params.avatar', 'user_params.level')
            ->orderBy('chats.id', 'desc')
            ->limit($count)
            ->get();

        $formattedMessages = $messages->map(function ($msg) {
            return [
                'id' => $msg->id,
                'user' => [
                    'name' => $msg->name,
                    'avatar' => $msg->avatar ?? '',
                    'level' => $msg->level ?? 1,
                ],
                'content' => $msg->content,
                'created_at' => $msg->created_at,
                'moderator' => $msg->moderator,
            ];
        });

        return $formattedMessages;
    }
}
