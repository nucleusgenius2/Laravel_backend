<?php

namespace App\DTO;

readonly class DataStringAndObject
{
    public bool $status;

    public ?string $error;

    public ?string $dataString;

    public ?object $dataObject;

    public ?int $code;

    public function __construct(
        bool $status,
        ?string $error=null,
        ?string $dataString=null,
        ?object $dataObject=null,
        ?int $code=null)
    {
        $this->status = $status;
        $this->error = $error;
        $this->dataString = $dataString;
        $this->dataObject = $dataObject;
        $this->code = $code;

    }
}
