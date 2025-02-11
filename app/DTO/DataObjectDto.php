<?php

namespace App\DTO;

readonly class DataObjectDto
{
    public bool $status;

    public ?string $error;

    public ?object $data;

    public function __construct(bool $status, ?string $error=null, ?object $data=null)
    {
        $this->status = $status;
        $this->error = $error;
        $this->data = $data;
    }
}
