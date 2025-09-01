<?php

namespace App\Http\Controllers;

use App\Models\Vip;
use App\Models\MissionUser;
use App\Models\UserVip;
use App\Models\Wallet;

class VipController extends Controller
{
    /**
     * Lista os níveis VIP disponíveis e o progresso do usuário.
     */
    public function getVipsWithProgress()
    {
        $user = auth('api')->user();
    
        if (!$user) {
            return response()->json(['message' => 'Usuário não autenticado.'], 401);
        }
    
        // Obter os níveis VIP ordenados por missões necessárias
        $vips = Vip::orderBy('required_missions', 'asc')->get();
    
        // Contar missões concluídas pelo usuário
        $completedMissions = MissionUser::where('user_id', $user->id)
            ->where('redeemed', 1)
            ->count();
    
        // Obter histórico de VIPs resgatados
        $userVipRecords = UserVip::where('user_id', $user->id)->pluck('last_reward_claimed_at', 'vip_id');
    
        // Determinar o VIP atual do cliente
        $currentVip = $vips->filter(function ($vip) use ($completedMissions) {
            return $completedMissions >= $vip->required_missions;
        })->last();
    
        // Construir a resposta
        $result = $vips->map(function ($vip) use ($completedMissions, $userVipRecords, $currentVip) {
            $progress = $vip->required_missions > 0 
                ? min(($completedMissions / $vip->required_missions) * 100, 100)
                : 100;
    
            $eligible = $completedMissions >= $vip->required_missions;
            $lastClaimed = $userVipRecords->get($vip->id);
    
            // Regra específica para o VIP atual
            $canClaim = true;
    
            if ($currentVip && $currentVip->id === $vip->id) {
                $canClaim = !$lastClaimed || now()->diffInDays($lastClaimed) >= 7;
            } else {
                // Não permitir resgate para níveis diferentes do atual
                $canClaim = false;
            }
    
            return [
                'id' => $vip->id,
                'title' => $vip->title,
                'description' => $vip->description,
                'required_missions' => $vip->required_missions,
                'weekly_reward' => (float) $vip->weekly_reward,
                'image' => $vip->image,
                'progress' => round($progress, 2),
                'eligible' => $eligible,
                'claimed' => !$canClaim, // Indica se o VIP foi resgatado nesta semana
            ];
        });
    
        return response()->json($result);
    }
    
    public function claimVipReward($vipId)
    {
        $user = auth('api')->user();
    
        if (!$user) {
            return response()->json(['message' => 'Usuário não autenticado.'], 401);
        }
    
        $vip = Vip::find($vipId);
    
        if (!$vip) {
            return response()->json(['message' => 'Nível VIP não encontrado.'], 404);
        }
    
        // Obter ou criar o registro do usuário para o VIP
        $userVip = UserVip::firstOrCreate(
            ['user_id' => $user->id, 'vip_id' => $vipId],
            ['last_reward_claimed_at' => null]
        );
    
        // Verificar se o VIP foi resgatado na semana passada
        if ($userVip->last_reward_claimed_at && now()->diffInDays($userVip->last_reward_claimed_at) < 7) {
            return response()->json(['message' => 'Você já resgatou a recompensa semanal deste nível VIP.'], 400);
        }
    
        // Verificar se o usuário alcançou este VIP
        $completedMissions = MissionUser::where('user_id', $user->id)
            ->where('redeemed', 1)
            ->count();
    
        if ($completedMissions < $vip->required_missions) {
            return response()->json(['message' => 'Você ainda não alcançou este nível VIP.'], 403);
        }
    
        // Atualizar saldos na carteira
        $wallet = Wallet::where('user_id', $user->id)->where('active', 1)->first();
    
        if (!$wallet) {
            return response()->json(['message' => 'Carteira não encontrada.'], 404);
        }
    
        $wallet->increment('balance_bonus', $vip->weekly_reward);
        $wallet->increment('balance_bonus_rollover', $vip->weekly_reward);
    
        // Atualizar a data de resgate
        $userVip->update(['last_reward_claimed_at' => now()]);
    
        return response()->json([
            'message' => 'Recompensa semanal resgatada com sucesso!',
            'reward' => $vip->weekly_reward,
            'wallet' => [
                'balance_bonus' => $wallet->balance_bonus,
                'balance_bonus_rollover' => $wallet->balance_bonus_rollover,
            ],
        ]);
    }
    
}
