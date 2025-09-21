<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyBonusConfig;
use App\Models\DailyBonusClaim;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyBonusController extends Controller
{
    /**
     * Verifica se o usuário pode resgatar o bônus.
     * Retorna JSON { can_claim: bool, remaining_hours, message }
     */
    public function check(Request $request)
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado.'], 401);
        }
    
        // Carrega config (assumimos que só exista 1 registro)
        $config = DailyBonusConfig::first();
        if (!$config) {
            return response()->json(['error' => 'Config de Bônus não encontrada.'], 500);
        }
    
        // Busca último resgate do usuário
        $lastClaim = DailyBonusClaim::where('user_id', $user->id)
            ->orderBy('claimed_at', 'desc')
            ->first();
    
        // Se nunca resgatou, pode resgatar imediatamente
        if (!$lastClaim) {
            return response()->json([
                'can_claim'       => true,
                'remaining_hours' => 0,
                'message'         => 'Você pode resgatar agora (primeiro resgate).',
                'bonus_value'     => $config->bonus_value,
            ]);
        }
    
        // Calcula diferença em horas entre agora e o claimed_at
        $diffHours = now()->diffInHours($lastClaim->claimed_at);
    
        if ($diffHours >= $config->cycle_hours) {
            // Já passou o intervalo
            return response()->json([
                'can_claim'       => true,
                'remaining_hours' => 0,
                'message'         => 'Você já pode resgatar o bônus novamente.',
                'bonus_value'     => $config->bonus_value,
            ]);
        } else {
            // Ainda não completou o ciclo
            $remaining = $config->cycle_hours - $diffHours;
            return response()->json([
                'can_claim'       => false,
                'remaining_hours' => $remaining,
                'message'         => "Faltam aproximadamente {$remaining} hora(s) para novo resgate.",
                'bonus_value'     => $config->bonus_value,
            ]);
        }
    }
    

    /**
     * Efetua o resgate do bônus (se possível).
     * Adiciona no balance_bonus e 2x em balance_bonus_rollover.
     */
    public function claim(Request $request)
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado.'], 401);
        }

        $config = DailyBonusConfig::first();
        if (!$config) {
            return response()->json(['error' => 'Config de Bônus não encontrada.'], 500);
        }

        // Busca último resgate
        $lastClaim = DailyBonusClaim::where('user_id', $user->id)
            ->orderBy('claimed_at', 'desc')
            ->first();

        if ($lastClaim) {
            $diffHours = now()->diffInHours($lastClaim->claimed_at);
            if ($diffHours < $config->cycle_hours) {
                $remaining = $config->cycle_hours - $diffHours;
                return response()->json([
                    'error' => "Aguarde {$remaining} hora(s) para resgatar novamente.",
                ], 422);
            }
        }

        // Pode resgatar => adiciona valor na carteira
        $wallet = Wallet::where('user_id', $user->id)->where('active', 1)->first();
        if (!$wallet) {
            return response()->json(['error' => 'Carteira não encontrada.'], 404);
        }

        // Soma valor em balance_bonus
        $wallet->balance_bonus += $config->bonus_value;
        // Soma 2x o valor em balance_bonus_rollover
        $wallet->balance_bonus_rollover += ($config->bonus_value * 2);
        $wallet->save();

        // Registra o resgate
        DailyBonusClaim::create([
            'user_id'    => $user->id,
            // claimed_at => default CURRENT_TIMESTAMP no DB ou use 'claimed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => "Bônus de R$ {$config->bonus_value} resgatado com sucesso!",
            'wallet'  => [
                'balance_bonus'         => $wallet->balance_bonus,
                'balance_bonus_rollover'=> $wallet->balance_bonus_rollover,
            ],
        ]);
    }
}
