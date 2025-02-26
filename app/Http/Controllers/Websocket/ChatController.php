<?php

namespace App\Http\Controllers\Websocket;

use App\DTO\WebsocketDto;
use App\Events\ChatMessageWebsocketSend;
use App\Http\Controllers\Controller;
use App\Http\Requests\ChatRequest;
use App\Http\Requests\CountRequest;
use App\Models\Chats;
use App\Services\BalanceService;
use App\Services\ChatService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    protected ChatService $service;

    public function __construct(ChatService $service){
        $this->service = $service;
    }

    public function index(CountRequest $request): JsonResponse
    {
        $data = $request->validated();

        $messages = $this->service->getDataChat($data['count']);

        $this->status = 'success';
        $this->code = 200;
        $this->dataJson = $messages;

        return $this->responseJsonApi();
    }

    public function store(ChatRequest $request): JsonResponse
    {
        $data = $request->validated();

        $dtaObjectDto = $this->service->createChatMessage(data: $data, user: $request->user() );

        if($dtaObjectDto->status){
            $this->status = 'success';
            $this->code = 200;
            $this->dataJson = $dtaObjectDto->data;
        }
        else{
            $this->code = $dtaObjectDto->code;
            $this->dataJson = $dtaObjectDto->error;
        }

        return $this->responseJsonApi();
    }


}
