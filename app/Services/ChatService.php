<?php

namespace App\Services;

use App\DTO\DataObjectDto;
use App\Models\Chats;
use App\Models\User;
use Carbon\Carbon;
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

    public function createChatMessage(array $data, User $user): DataObjectDto
    {
        $message = Chats::create([
            'content' => $data['content'],
            'user' => $user->id,
            'created_at' => Carbon::now(),
        ]);

        if($message) {
            return new DataObjectDto(status: true, data: $message);
        }
        else{
            return new DataObjectDto(status: false, error: 'Сообщение не сохранено', code: 400);
        }
    }
}
