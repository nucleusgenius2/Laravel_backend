<?php

namespace App\Services;

use App\DTO\DataArrayDto;
use App\DTO\DataObjectDto;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;

class NotificationService
{
     public function getNotifications(User $user, int $page, int $perPage): DataObjectDto
     {
         $notifications = Notification::where('user_id', $user->id)
             ->orderBy('id', 'desc')
             ->paginate($perPage, ['*'], 'page',  $page);

         return new DataObjectDto(status: true, data: $notifications );
     }

    public function showNotification(int $id): DataObjectDto
    {
        $notifications = Notification::where('id', $id)->first();
        if($notifications){
            return new DataObjectDto(status: true, data: $notifications );
        }
        else{
            return new DataObjectDto(status: false, error: 'Уведомление не найдено' );
        }

    }

    public function createNotification(array $data): DataObjectDto
    {
        $notifications = Notification::create([
            'user_id' => $data['user_id'],
            'content' => $data['content'],
            'created_at' => Carbon::now(),
            'read' => 0
        ]);

        if($notifications){
            return new DataObjectDto(status: true, data: $notifications );
        }
        else{
            return new DataObjectDto(status: false, error: 'Уведомление не создано' );
        }

    }

}
