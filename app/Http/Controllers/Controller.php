<?php

namespace App\Http\Controllers;
use App\Traits\StructuredResponse;

/**
 * @OA\Info(
 *    title="Your super  ApplicationAPI",
 *    version="1.0.0",
 * )
 */

abstract class Controller
{
    use StructuredResponse;
}
