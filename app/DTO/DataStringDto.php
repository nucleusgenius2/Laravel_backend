<?php

namespace App\DTO;

readonly class DataStringDto
{
    public bool $status;

    public ?string $error;

    public ?string $data;

    public ?int $code;

    public function __construct(bool $status, ?string $error=null, ?string $data=null, ?int $code=null)
    {
        $this->status = $status;
        $this->error = $error;
        $this->data = $data;
        $this->code = $code;
    }
}
