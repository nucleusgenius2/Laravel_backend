<?php

namespace App\Exceptions;

use App\Traits\StructuredResponse;
use Illuminate\Http\Exceptions\HttpResponseException;

class ValidationExceptionResponse extends HttpResponseException
{

    public function __construct($errors)
    {
        parent::__construct(
            response()->json([
                'data' => [
                    'status' => 'error',
                    'message' => $errors,
                    'json' => []
                ],
            ], 422)

        );
    }
}
