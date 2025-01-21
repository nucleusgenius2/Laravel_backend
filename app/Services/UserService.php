<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class UserService
{
    public static function getUserLevel(int $userId): array|null
    {
        // Выполнение хранимой процедуры
        $result = DB::select('CALL getUserLevel(?)', [$userId]);

        if (count($result) > 0) {
            $userLevel = $result[0]->user_level;
            $maxAmount = $result[0]->maxAmount;
            $fullAmount = $result[0]->fullAmount;

            return [
                'user_level' => $userLevel,
                'max_amount' => $maxAmount,
                'full_amount' => $fullAmount,
            ];
        }

        return null;
    }
}
