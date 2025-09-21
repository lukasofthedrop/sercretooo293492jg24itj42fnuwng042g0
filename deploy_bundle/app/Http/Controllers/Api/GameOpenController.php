<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GameOpenConfig;
use App\Models\Deposit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GameOpenController extends Controller
{
    public function checkDailyDeposit(Request $request)
    {
        // Verifica se o usuário está autenticado via 'auth:api' ou 'auth:jwt'
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado.'], 401);
        }

        // Carrega a config de abertura de jogos
        $config = GameOpenConfig::first();
        if (!$config) {
            return response()->json(['error' => 'Configuração não encontrada.'], 500);
        }

        // Se não exigir depósito hoje, pode abrir
        if (!$config->requires_deposit_today) {
            return response()->json([
                'can_open' => true,
                'message'  => 'Depósito diário não é exigido.'
            ]);
        }

        // Se exige, verifica se existe deposit(status=1) hoje
        $hasDepositToday = Deposit::where('user_id', $user->id)
            ->where('status', 1)
            ->whereDate('created_at', Carbon::today())
            ->exists();

        if ($hasDepositToday) {
            return response()->json([
                'can_open' => true,
               
            ]);
        } else {
            return response()->json([
                'can_open' => false,
               
            ]);
        }
    }
}
