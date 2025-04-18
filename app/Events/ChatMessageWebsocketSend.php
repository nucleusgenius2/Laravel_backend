<?php

namespace App\Events;


use App\Contracts\EventWebsocket;
use App\DTO\WebsocketDto;
use App\Models\UserParam;
use Illuminate\Foundation\Events\Dispatchable;

class ChatMessageWebsocketSend implements EventWebsocket
{
    use Dispatchable;

    public WebsocketDto $dto;

    public function __construct(WebsocketDto $dto)
    {
        $this->dto = $dto;
    }

    public function getType():string
    {
        return 'publicMessage';
    }

    public function getUserId():int
    {
        return $this->dto->userId;
    }

    public function getUserName():string
    {
        return $this->dto->userName;
    }

    public function getData():string
    {
        return $this->dto->data;
    }

    public function getUserDetails(): UserParam
    {
        return UserParam::where('id', $this->dto->userId)->first();
    }


}
