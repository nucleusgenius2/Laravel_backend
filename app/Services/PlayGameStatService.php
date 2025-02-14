<?php

namespace App\Services;

use App\DTO\DataObjectDto;
use App\Models\PlayGame;
use App\Models\User;

class PlayGameStatService
{
    public function getStatGame(User $user): DataObjectDto
    {
        $userParams = $user->params()->first();

        $stat = PlayGame::where([
            ['play_game.user_id', $user->id],
            ['play_game.currency_id', $userParams->currency_id]
        ])->selectRaw('COUNT(*) as total_count, SUM(win) as total_win, MAX(win) as max_win')
            ->first();

        return new DataObjectDto(status: true, data: $stat);

    }
}
