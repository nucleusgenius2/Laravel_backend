<?php

namespace App\Http\Controllers;

use App\DTO\DataObjectDto;
use App\Http\Requests\NotificationRequest;
use App\Http\Requests\PageRequest;
use App\Models\Notification;
use App\Services\BalanceService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public int $perPageFrontend = 20;

    protected NotificationService $service;

    public function __construct(NotificationService $service){
        $this->service = $service;
    }

    public function index(PageRequest $request): JsonResponse
    {
        $data = $request->validated();

        $dataObjectDto = $this->service->getNotifications(user: $request->user(), page: $data['page'], perPage: $this->perPageFrontend);

        if($dataObjectDto->status) {
            $this->status = 'success';
            $this->code = 200;
            $this->dataJson = $dataObjectDto->data;
        }

        return $this->responseJsonApi();
    }

    public function show(int $id): JsonResponse
    {
        if($id > 0) {
            //добавить сюда врата на проверку прав просмотра, когда будут роли юзеров

            $dataObjectDto = $this->service->showNotification(id: $id);

            if($dataObjectDto->status){
                $this->status = 'success';
                $this->code = 200;
                $this->dataJson = $dataObjectDto->data;
            }
            else{
                $this->code = 404;
                $this->message = $dataObjectDto->error;
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

        $dataObjectDto = $this->service->createNotification(data: $data);

        if($dataObjectDto->status){
            $this->status = 'success';
            $this->code = 200;
            $this->dataJson = $dataObjectDto->data;
        }
        else{
            $this->code = 500;
            $this->message = $dataObjectDto->error;
        }

        return $this->responseJsonApi();
    }
}
