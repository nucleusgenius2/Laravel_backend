<?php

namespace App\DTO;

readonly class WebsocketDto
{
    public int $userId;
    public string $userName;

    public string $data;

    public function __construct(int $userId, string $userName, string $data)
    {
        $this->userId = $userId;
        $this->userName = $userName;
        $this->data = $data;
    }
}
