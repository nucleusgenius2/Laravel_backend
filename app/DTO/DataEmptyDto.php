<?php

namespace App\DTO;

readonly class DataEmptyDto
{
    public bool $status;

    public ?string $error;

    public ?int $code;

    public function __construct(bool $status, ?string $error=null, ?int $code=null)
    {
        $this->status = $status;
        $this->error = $error;
        $this->code = $code;
    }
}
