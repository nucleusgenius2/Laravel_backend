<?php

namespace App\Services;

class GenerateUniqueString
{
   public static function generate(int $userId, int $length): string
    {
        return substr(md5($userId . uniqid('', true)), 0, $length);
    }
}
