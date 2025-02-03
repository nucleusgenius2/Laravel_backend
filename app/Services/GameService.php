<?php

namespace App\Services;

use App\Models\PlayGame;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class GameService
{
    public function getTableWinner(int $count): Collection
    {
        $winners = PlayGame::select(
            'play_game.date_play',
            'play_game.win',
            'games.title',
            'games.img',
            'users.name',
            'user_params.avatar',
            'user_params.level',
        )
            ->where('games.type','minigame')
            ->join('games','games.gameId', '=','play_game.gameId')
            ->join('users', 'users.id', '=','play_game.user_id')
            ->join('user_params', 'play_game.user_id', '=','user_params.id')
            ->orderBy('play_game.id','desc')
            ->limit($count)
            ->get();

        $winnersStructured = $winners->map(function($winner) {
            return [
                'date_play' => $winner->date_play,
                'win' => $winner->win,
                'game' => [
                    'title' => $winner->title,
                    'img' => $winner->img,
                ],
                'user' => [
                    'name' => $winner->name,
                    'avatar' => $winner->avatar,
                    'level' => $winner->level,
                ]
            ];
        });

        return $winnersStructured;
    }

    public function getTableSlider(int $count): Collection
    {
        $winners = PlayGame::select(
            'play_game.date_play',
            'play_game.win',
            'games.title',
            'games.img',
            'users.name',
            'user_params.avatar',
            'user_params.level',
        )
            ->where('games.type','minigame')
            ->join('games','games.gameId', '=','play_game.gameId')
            ->join('users', 'users.id', '=','play_game.user_id')
            ->join('user_params', 'play_game.user_id', '=','user_params.id')
            ->orderBy('play_game.id','desc')
            ->limit($count)
            ->get();

        $winnersStructured = $winners->map(function($winner) {
            return [
                'date_play' => $winner->date_play,
                'win' => $winner->win,
                'game' => [
                    'title' => $winner->title,
                    'img' => $winner->img,
                ],
                'user' => [
                    'name' => $winner->name,
                    'avatar' => $winner->avatar,
                    'level' => $winner->level,
                ]
            ];
        });

        return $winnersStructured;
    }

    public function getPlayGame(int $id): array
    {
        $winner = PlayGame::select(
            'play_game.date_play',
            'play_game.win',
            'games.title',
            'games.img',
            'users.name',
            'user_params.avatar',
            'user_params.level',
        )
            ->where('play_game.id', $id)
            ->join('games','games.gameId', '=','play_game.gameId')
            ->join('users', 'users.id', '=','play_game.user_id')
            ->join('user_params', 'play_game.user_id', '=','user_params.id')
            ->first();

        if($winner) {
            return [
                'date_play' => $winner->date_play,
                'win' => $winner->win,
                'game' => [
                    'title' => $winner->title,
                    'img' => $winner->img,
                ],
                'user' => [
                    'name' => $winner->name,
                    'avatar' => $winner->avatar,
                    'level' => $winner->level,
                ]
            ];
        }
        else{
            return [];
        }


    }

}
