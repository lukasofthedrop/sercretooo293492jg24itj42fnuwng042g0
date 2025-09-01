<?php

namespace App\Http\Controllers;

use App\Models\Mission;
use App\Models\MissionUser;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Carbon\Carbon;

class MissionController extends Controller
{
    /**
     * Lista todas as missões disponíveis para o usuário autenticado.
     */
    public function index()
    {
        $user = auth('api')->user();
    
        if (!$user) {
            return response()->json(['message' => 'Usuário não autenticado.'], 401);
        }
    
        $missions = Mission::where('status', 'active')->get();
    
        $result = $missions->map(function ($mission) use ($user) {
            $currentProgress = 0;
    
            // Cálculo do progresso com base no tipo da missão
            switch ($mission->type) {
                case 'game_bet': // Missão de Aposta por Jogo
                    $currentProgress = Order::where('user_id', $user->id)
                        ->where('type', 'bet')
                        ->where('game', $mission->game_id)
                        ->whereDate('created_at', Carbon::today())
                        ->sum('amount');
                    break;
    
                case 'total_bet': // Missão de Aposta Total (inclui bet e win)
                    $currentProgress = Order::where('user_id', $user->id)
                        ->whereIn('type', ['bet', 'win']) // Inclui apostas e ganhos
                        ->whereDate('created_at', Carbon::today())
                        ->sum('amount');
                    break;
    
                case 'deposit': // Missão de Depósito
                    $currentProgress = Transaction::where('user_id', $user->id)
                        ->whereDate('created_at', Carbon::today())
                        ->sum('price');
                    break;
    
                case 'rounds_played': // Missão de Rodadas Jogadas
                    $currentProgress = Order::where('user_id', $user->id)
                        ->where('game', $mission->game_id)
                        ->whereIn('type', ['bet', 'win'])
                        ->whereDate('created_at', Carbon::today())
                        ->count();
                    break;
    
                case 'win_amount': // Missão de Ganho por Jogo
                    $currentProgress = Order::where('user_id', $user->id)
                        ->where('type', 'win')
                        ->where('game', $mission->game_id)
                        ->whereDate('created_at', Carbon::today())
                        ->sum('amount');
                    break;
    
                case 'loss_amount': // Missão de Perda por Jogo
                    $bets = Order::where('user_id', $user->id)
                        ->where('type', 'bet')
                        ->where('game', $mission->game_id)
                        ->whereDate('created_at', Carbon::today())
                        ->sum('amount');
                    $wins = Order::where('user_id', $user->id)
                        ->where('type', 'win')
                        ->where('game', $mission->game_id)
                        ->whereDate('created_at', Carbon::today())
                        ->sum('amount');
                    $currentProgress = max(0, $bets - $wins);
                    break;
            }
    
            // Calcula o progresso como porcentagem
            $progress = $mission->target_amount > 0 
                ? min(($currentProgress / $mission->target_amount) * 100, 100) 
                : 0;
    
            // Verifica se a missão foi completada
            $completed = $progress >= 100;
    
            return [
                'id' => $mission->id,
                'title' => $mission->title,
                'description' => $mission->description,
                'type' => $mission->type,
                'target_amount' => (float) $mission->target_amount,
                'reward' => (float) $mission->reward,
                'image' => $mission->image,
                'progress' => round($progress, 2), // Progresso como porcentagem arredondada
                'completed' => $completed,
            ];
        });
    
        return response()->json($result);
    }

    /**
     * Atualiza o progresso de uma missão.
     */
    public function updateProgress($missionId)
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json(['message' => 'Usuário não autenticado.'], 401);
        }

        $mission = Mission::findOrFail($missionId);
        $progress = MissionUser::firstOrNew(['user_id' => $user->id, 'mission_id' => $mission->id]);

        switch ($mission->type) {
            case 'deposit':
                $progress->reward = $mission->reward;
                break;

            case 'game_bet':
                $progress->current_progress = Order::where('user_id', $user->id)
                    ->where('game', $mission->game_id)
                    ->where('type', 'bet')
                    ->whereDate('created_at', Carbon::today()) // Filtra pelo dia atual
                    ->sum('amount');
                break;

            case 'total_bet':
                $progress->current_progress = Order::where('user_id', $user->id)
                    ->where('type', 'bet')
                    ->whereDate('created_at', Carbon::today()) // Filtra pelo dia atual
                    ->sum('amount');
                break;
        }

        $progress->save();

        return response()->json(['success' => true, 'message' => 'Progresso atualizado.', 'progress' => $progress]);
    }


    /**
     * Verifica se uma missão foi resgatada.
     */
    public function checkIfRedeemed($missionId)
    {
        $user = auth('api')->user();
    
        if (!$user) {
            return response()->json(['message' => 'Usuário não autenticado.'], 401);
        }
    
        $today = Carbon::today();
    
        $missionUser = MissionUser::where('user_id', $user->id)
            ->where('mission_id', $missionId)
            ->whereDate('updated_at', $today) // Verifica se foi atualizado hoje
            ->first();
    
        return response()->json([
            'redeemed' => $missionUser && $missionUser->redeemed === 1, // Resgatada hoje
        ]);
    }
    
    

    /**
     * Resgata a recompensa de uma missão.
     */
    public function redeemReward($missionId)
    {
        $user = auth('api')->user();
    
        if (!$user) {
            return response()->json(['message' => 'Usuário não autenticado.'], 401);
        }
    
        // Verifique se a missão existe
        $mission = Mission::find($missionId);
    
        if (!$mission) {
            return response()->json(['message' => 'Missão não encontrada.'], 404);
        }
    
        // Verifique se há um registro da missão para hoje
        $missionUser = MissionUser::where('user_id', $user->id)
            ->where('mission_id', $missionId)
            ->whereDate('created_at', Carbon::today())
            ->first();
    
        // Se já foi resgatada hoje, retorna erro
        if ($missionUser && $missionUser->redeemed) {
            return response()->json(['message' => 'Missão já resgatada hoje.'], 400);
        }
    
        // Obtenha a carteira do usuário
        $wallet = Wallet::where('user_id', $user->id)->where('active', 1)->first();
    
        if (!$wallet) {
            return response()->json(['message' => 'Carteira não encontrada.'], 404);
        }
    
        // Se não há registro, cria um novo
        if (!$missionUser) {
            $missionUser = MissionUser::create([
                'user_id' => $user->id,
                'mission_id' => $missionId,
                'current_progress' => $mission->target_amount, // Progresso completo
                'redeemed' => 1, // Marca como resgatado
            ]);
        } else {
            // Atualiza o registro existente para resgatado
            $missionUser->update(['redeemed' => 1]);
        }
    
        // Atualize os saldos da carteira
        $wallet->increment('balance_bonus', $mission->reward);
        $wallet->increment('balance_bonus_rollover', $mission->reward);
    
        return response()->json([
            'message' => 'Recompensa resgatada com sucesso!',
            'wallet' => [
                'balance_bonus' => $wallet->balance_bonus,
                'balance_bonus_rollover' => $wallet->balance_bonus_rollover,
            ],
        ]);
    }
    
    
    
    
    
    
}
