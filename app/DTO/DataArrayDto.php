<?php

namespace App\DTO;

readonly class DataArrayDto
{
    public bool $status;

    public ?string $error;

    public ?array $data;

    public function __construct(bool $status, ?string $error=null, ?array $data=null)
    {
        $this->status = $status;
        $this->error = $error;
        $this->data = $data;
    }
}
