<?php

namespace App\Http\Controllers;

use App\Http\Requests\NotificationRequest;
use App\Http\Requests\PageRequest;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public int $perPageFrontend = 20;

    public function index(PageRequest $request): JsonResponse
    {
        $data = $request->validated();

        $notifications = Notification::where('user_id', $request->user()->id)
            ->orderBy('id', 'desc')
            ->paginate($this->perPageFrontend, ['*'], 'page', $data['page']);

        $this->status = 'success';
        $this->code = 200;
        $this->dataJson = $notifications;

        return $this->responseJsonApi();
    }

    public function show(int $id): JsonResponse
    {
        if($id > 0) {
            //добавить сюда врата на проверку прав просмотра, когда будут роли юзеров
            $notifications = Notification::where('id', $id)->first();
            if($notifications){
                $this->status = 'success';
                $this->code = 200;
                $this->dataJson = $notifications;
            }
            else{
                $this->code = 404;
                $this->message = 'Не найдено уведомления';
            }

        }
        else{
            $this->code = 422;
            $this->message = 'Не валидный id';
        }

        return $this->responseJsonApi();
    }

    public function store(NotificationRequest $request): JsonResponse
    {
        $data = $request->validated();

        //добавить сюда врата на проверку прав просмотра, когда будут роли юзеров
        $notifications = Notification::create([
            'user_id' => $data['user_id'],
            'content' => $data['content'],
            'created_at' => Carbon::now(),
            'read' => 0
        ]);

        if($notifications){
            $this->status = 'success';
            $this->code = 200;
            $this->dataJson = $notifications;
        }
        else{
            $this->code = 500;
            $this->message = 'Уведомление не было создано';
        }

        return $this->responseJsonApi();
    }
}
