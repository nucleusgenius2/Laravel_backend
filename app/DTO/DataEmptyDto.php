<?php

namespace App\DTO;

readonly class DataEmptyDto
{
    public bool $status;

    public ?string $error;

    public function __construct(bool $status, ?string $error=null)
    {
        $this->status = $status;
        $this->error = $error;
    }
}
