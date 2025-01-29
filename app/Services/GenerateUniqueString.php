<?php

namespace App\Services;

class GenerateUniqueString
{
    /**
     * Генерация уникальной строки
     * @param int $userId
     * @param int $length
     * @return string
     */
    public static function generate(int $userId, int $length): string
    {
        return substr(md5($userId . uniqid('', true)), 0, $length);
    }

    public static function generateName(int $length): string
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
    }
}
