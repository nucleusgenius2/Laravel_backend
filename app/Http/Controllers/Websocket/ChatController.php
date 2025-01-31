<?php

namespace App\Http\Controllers\Websocket;

use App\DTO\WebsocketDto;
use App\Events\ChatMessageSent;
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
    protected BalanceService $service;

    public function __construct(BalanceService $service){
        $this->service = $service;
    }


    public function index(CountRequest $request, ChatService $service): JsonResponse
    {
        $data = $request->validated();

       // $messages = Chats::orderBy('id', 'desc')->limit($data['count'])->get();

        $messages = $service->getDataChat($data['count']);

        $this->status = 'success';
        $this->code = 200;
        $this->dataJson = $messages;

        return $this->responseJsonApi();
    }

    public function store(ChatRequest $request): JsonResponse
    {
        $data = $request->validated();

        $messages = Chats::create([
           'content' => $data['content'],
           'user' => $request->user()->id,
           'created_at' => Carbon::now(),
        ]);

        $userDto = new WebsocketDto($request->user()->id, $request->user()->name, $data['content']);
        event(new ChatMessageSent($userDto));

        $this->status = 'success';
        $this->code = 200;
        $this->dataJson = $messages;

        return $this->responseJsonApi();
    }


}
