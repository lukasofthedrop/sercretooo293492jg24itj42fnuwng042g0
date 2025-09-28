<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class GameWebController extends Controller
{
    public function show(Request $request, string $key)
    {
        $game = null;

        if (ctype_digit($key)) {
            $game = Game::where('status', 1)->find($key);
        }

        if (! $game) {
            $game = Game::where('status', 1)->where('game_code', $key)->first();
        }

        if (! $game) {
            abort(404);
        }

        return view('game.launch', [
            'gameId' => $game->id,
            'gameName' => $game->game_name,
        ]);
    }
}

