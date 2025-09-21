<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BetHistory;
use Carbon\Carbon;

class MinesHistoryController extends Controller
{
    // Exemplo de cálculo do multiplicador:
    //  - 0.10x para cada estrela
    //  - +1.50x a cada 5 estrelas
    //  => total = 1 + (0.1 * stars) + (1.5 * floor(stars/5))
    private function computeMultiplier($stars)
    {
        return 1 + (0.1 * $stars) + (1.5 * floor($stars / 5));
    }

    public function getHistory(Request $request)
    {
        // Verifica o usuário autenticado
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['message' => 'Usuário não autenticado.'], 401);
        }

        // 10 últimas apostas do usuário
        $userHistoryRaw = BetHistory::where('user_id', $user->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Mapeia para incluir user_name, multiplier e profit
        $userHistory = $userHistoryRaw->map(function ($item) {
            $stars = $item->stars_revealed; 
            $multiplier = $this->computeMultiplier($stars);
            $profit = $item->payout - $item->bet_amount;

            return [
                'id'             => $item->id,
                'user_name'      => optional($item->user)->name, 
                'stars_revealed' => $stars, // diamantes achados
                'multiplier'     => $multiplier, 
                'bet_amount'     => $item->bet_amount,
                'payout'         => $item->payout,
                'profit'         => $profit,
                'is_win'         => (bool) $item->is_win,
                'created_at'     => $item->created_at,
            ];
        });

        // Top 10 do dia
        $today = Carbon::today();
        $topHistoryRaw = BetHistory::whereDate('created_at', $today)
            ->whereColumn('payout', '>', 'bet_amount') // Apenas apostas com lucro
            ->with('user')
            ->orderByRaw('(payout - bet_amount) DESC')   // Ordena pelo lucro (maior para menor)
            ->take(10)
            ->get();
        // Mapeia
        $topHistory = $topHistoryRaw->map(function ($item) {
            $stars = $item->stars_revealed;
            $multiplier = $this->computeMultiplier($stars);
            $profit = $item->payout - $item->bet_amount;

            return [
                'id'             => $item->id,
                'user_name'      => optional($item->user)->name,
                'stars_revealed' => $stars,
                'multiplier'     => $multiplier,
                'bet_amount'     => $item->bet_amount,
                'payout'         => $item->payout,
                'profit'         => $profit,
                'is_win'         => (bool) $item->is_win,
                'created_at'     => $item->created_at,
            ];
        });

        return response()->json([
            'user_history' => $userHistory,
            'top_history'  => $topHistory,
        ]);
    }
}
