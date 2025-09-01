<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GameConfig;
use App\Models\BetHistory;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MinesController extends Controller
{
    /**
     * Inicia um novo jogo de Mines.
     * Deduz a aposta e define as bombas de acordo com o modo.
     */
    public function startGame(Request $request)
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['message' => 'Usuário não autenticado.'], 401);
        }

        $betAmount = floatval($request->input('bet_amount'));

        // Carrega config
        $config = GameConfig::first();
        if (!$config) {
            return response()->json(['error' => 'Configuração do jogo não encontrada.'], 500);
        }

        // Valida a aposta
        if ($betAmount < 0.50 || $betAmount > 100) {
            return response()->json(['error' => 'Valor de aposta inválido.'], 422);
        }

        // Carteira
        $wallet = Wallet::where('user_id', $user->id)->where('active', 1)->first();
        if (!$wallet) {
            return response()->json(['error' => 'Carteira não encontrada.'], 404);
        }
        $totalAvailable = ($wallet->balance ?? 0) + ($wallet->balance_bonus ?? 0) + ($wallet->balance_withdrawal ?? 0);
        if ($totalAvailable < $betAmount) {
            return response()->json(['error' => 'Saldo insuficiente.'], 422);
        }

        // Deduz a aposta
        $bet = $this->deductBetFromWallet($user, $betAmount);
        if ($bet == null) {
            return response()->json(['error' => 'Erro ao debitar a aposta.'], 500);
        }

        // Define bombsCount de acordo com modo_atual
        $bombsCount = ($config->modo_atual === 'distribuicao')
            ? $config->minas_distribuicao
            : $config->minas_arrecadacao;

        // Aplica bet_loss se não for influenciador/perdedor
        $forceBomb = false;
        if (!$config->modo_influenciador && !$config->modo_perdedor) {
            $random = mt_rand(0, 100);
            if ($random < $config->bet_loss) {
                $forceBomb = true;
            }
        }

        // Sorteia posições das bombas
        $indices = range(0, 24);
        shuffle($indices);
        $bombPositions = array_slice($indices, 0, $bombsCount);
        if ($forceBomb && !in_array(0, $bombPositions)) {
            array_pop($bombPositions);
            $bombPositions[] = 0;
        }
        sort($bombPositions);

        // Cria BetHistory
        $betHistory = BetHistory::create([
            'user_id'       => $user->id,
            'bet_amount'    => $betAmount,
            'bombs_count'   => $bombsCount,
            'payout'        => 0,
            'stars_revealed'=> 0,
            'is_win'        => false,
            'typeWallet'    => $bet, 
            'house_profit'  => 0,
            'game_data'     => ['bomb_positions' => $bombPositions],
        ]);

        return response()->json([
            'success'       => true,
            'game_id'       => $betHistory->id,
            'bombs_count'   => $bombsCount,
            'total_balance' => $wallet->total_balance,
        ]);
    }

    /**
     * Deduz a aposta da carteira.
     */
    protected function deductBetFromWallet($user, $betAmount)
    {
        $wallet = Wallet::where('user_id', $user->id)->where('active', 1)->first();
        if (!$wallet) {
            return false;
        }

        $remaining = $betAmount;
        $balance = $wallet->balance ?? 0;
        $bonus   = $wallet->balance_bonus ?? 0;
        $withd   = $wallet->balance_withdrawal ?? 0;
        $saldoAnt = (float)$balance + (float)$bonus + (float)$withd; 
        $typeWallet = null;                   
        if (($balance + $bonus + $withd) < $betAmount) {
            return false;
        }

         if ($balance >= $remaining) {
            $wallet->decrement('balance', $remaining);
            $typeWallet = 'balance';
         } else if ($bonus >= $remaining) {
            $wallet->decrement('balance_bonus', $remaining);
            $typeWallet = 'balance_bonus';
         } else if ($wallet->withd >= $remaining) {
            $wallet->decrement('balance_withdrawal', $remaining);
            $typeWallet = 'balance_withdrawal';
         } else {
            $typeWallet = "balance";
            if ($saldoAnt >= $remaining) {
                $valorPerdido = $remaining;
                if ($bonus >= $valorPerdido) {
                    $bonus -= $valorPerdido;
                    $valorPerdido = 0;
                } else {
                    $valorPerdido -= $bonus;
                    $bonus = 0;
                }
                if ($balance >= $valorPerdido) {
                    $balance -= $valorPerdido;
                    $valorPerdido = 0;
                } else {
                    $valorPerdido -= $balance;
                    $balance = 0;
                }
                if ($withd >= $valorPerdido) {
                    $withd -= $valorPerdido;
                    $valorPerdido = 0;
                } else {
                    $valorPerdido -= $withd;
                    $withd = 0;
                }
                $wallet->update([
                    "balance_bonus" => $bonus,
                    "balance" => $balance,
                    "balance_withdrawal" => $withd
                ]);
            }
                
         }

        $wallet->save();
        return $typeWallet;
    }

    /**
     * Revela célula.
     * Se modo_influenciador => sempre ganha;
     * Se modo_perdedor => sempre perde;
     * Caso normal => verifica se é bomba.
     * Se for bomba no modo_arrecadacao => soma bet_amount ao total_arrecadado e verifica meta.
     * Se for bomba no modo_distribuicao => soma 0.
     */
    public function revealCell(Request $request)
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado.'], 401);
        }
    
        $gameId    = $request->input('game_id');
        $cellIndex = intval($request->input('cell_index'));
    
        if ($cellIndex < 0 || $cellIndex > 24) {
            return response()->json(['error' => 'Índice de célula inválido.'], 422);
        }
    
        $betHistory = BetHistory::find($gameId);
        if (!$betHistory) {
            return response()->json(['error' => 'Jogo não encontrado.'], 404);
        }
    
        $config = GameConfig::first();
        if (!$config) {
            return response()->json(['error' => 'Config não encontrada.'], 500);
        }
    
        // Carrega e inicializa o game_data
        $gameData = $betHistory->game_data;
        if (!is_array($gameData)) {
            $gameData = [];
        }
        $revealedPositions = $gameData['revealed_positions'] ?? [];
    
        // Proteção: se a célula já foi revelada, rejeita a requisição
        if (in_array($cellIndex, $revealedPositions)) {
            return response()->json(['error' => 'Célula já revelada.'], 422);
        }
        // Adiciona a célula à lista de reveladas
        $revealedPositions[] = $cellIndex;
        $gameData['revealed_positions'] = $revealedPositions;
    
        // Modo perdedor: o usuário sempre perde
        if ($config->modo_perdedor) {
            if ($config->modo_atual === 'arrecadacao') {
                $config->total_arrecadado += $betHistory->bet_amount;
                $this->checkModeSwitch($config);
            }
            $betHistory->update([
                'payout'       => 0,
                'is_win'       => false,
                'house_profit' => $betHistory->bet_amount,
                'game_data'    => $gameData,
            ]);
            return response()->json([
                'success'     => false,
                'message'     => 'BOMBA revelada! Você perdeu (modo perdedor).',
                'cell_status' => 'bomb',
            ]);
        }
    
        // Modo influenciador: o usuário sempre ganha
        if ($config->modo_influenciador) {
            $starsRevealed = $betHistory->stars_revealed + 1;
            $payout = $this->calculatePayout($betHistory->bet_amount, $starsRevealed, $config);
            $betHistory->update([
                'stars_revealed' => $starsRevealed,
                'payout'         => $payout,
                'is_win'         => true,
                'game_data'      => $gameData,
            ]);
            return response()->json([
                'success'        => true,
                'cell_status'    => 'safe',
                'stars_revealed' => $starsRevealed,
                'current_payout' => $payout,
            ]);
        }
    
        // Modo normal
        $bombPositions = $betHistory->game_data['bomb_positions'] ?? [];
        if (in_array($cellIndex, $bombPositions)) {
            // Se for bomba, o usuário perde.
            if ($config->modo_atual === 'arrecadacao') {
                $config->total_arrecadado += $betHistory->bet_amount;
                $this->checkModeSwitch($config);
            }
            $betHistory->update([
                'payout'       => 0,
                'is_win'       => false,
                'house_profit' => $betHistory->bet_amount,
                'game_data'    => $gameData,
            ]);
            return response()->json([
                'success'     => false,
                'message'     => 'BOMBA revelada! Você perdeu.',
                'cell_status' => 'bomb',
            ]);
        } else {
            // Se for segura, incrementa o número de diamantes (stars_revealed)
            $starsRevealed = $betHistory->stars_revealed + 1;
            $payout = $this->calculatePayout($betHistory->bet_amount, $starsRevealed, $config);
            $betHistory->update([
                'stars_revealed' => $starsRevealed,
                'payout'         => $payout,
                'is_win'         => true,
                'game_data'      => $gameData,
            ]);
            return response()->json([
                'success'        => true,
                'cell_status'    => 'safe',
                'stars_revealed' => $starsRevealed,
                'current_payout' => $payout,
            ]);
        }
    }
    

    /**
     * Finaliza o jogo (cash out).
     * Se modo_atual=distribuicao => soma (bet_amount - payout) se positivo, ao total_distribuido.
     */
     /**
     * Finaliza o jogo (cashOut).
     * Se modo_atual=distribuicao => soma (payout - bet_amount) se for positivo.
     */
    public function cashOut(Request $request)
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['error' => 'Usuário não autenticado.'], 401);
        }

        $gameId = $request->input('game_id');
        $betHistory = BetHistory::find($gameId);
        if (!$betHistory) {
            return response()->json(['error' => 'Jogo não encontrado.'], 404);
        }

        if ($betHistory->payout <= 0) {
            return response()->json(['error' => 'Jogo já finalizado ou perdido.'], 422);
        }

        $wallet = Wallet::where('user_id', $user->id)->where('active', 1)->first();
        if (!$wallet) {
            return response()->json(['error' => 'Carteira não encontrada.'], 404);
        }

        $config = GameConfig::first();
        if (!$config) {
            return response()->json(['error' => 'Config não encontrada.'], 500);
        }

        $payout = $betHistory->payout;
        if($betHistory->typeWallet == "balance"){
            $wallet->balance += $payout;
            $wallet->balance_deposit_rollover -= $payout;
        }else if($betHistory->typeWallet == "balance_bonus"){
            $wallet->balance_bonus += $payout;
            $wallet->balance_bonus_rollover -=  $payout;

        }else {
             $wallet->balance_withdrawal += $payout;
        
        }
        if($wallet->balance_deposit_rollover <= 0){
            $wallet->balance_withdrawal += $wallet->balance;
            $wallet->balance = 0;
            $wallet->balance_deposit_rollover = 0;
        }
        if($wallet->balance_bonus_rollover <= 0){
            $wallet->balance_withdrawal += $wallet->balance_bonus;
            $wallet->balance_bonus = 0;
            $wallet->balance_bonus_rollover = 0;
        }
        $wallet->save();

        // Se modo_atual=distribuicao => soma difference = (payout - bet_amount) se > 0
        $difference = $payout - $betHistory->bet_amount;
        if ($difference < 0) {
            $difference = 0; // se for negativo => 0
        }

        if ($config->modo_atual === 'distribuicao' && $difference > 0) {
            $config->total_distribuido += $difference;
            $this->checkModeSwitch($config);
        }

        // house_profit
        $houseProfit = $betHistory->bet_amount - $payout; // se payout>bet, pode ser negativo
        $betHistory->update([
            'payout'       => $payout,
            'house_profit' => $houseProfit,
        ]);

        return response()->json([
            'success'     => true,
            'payout'      => $payout,
            'new_balance' => $wallet->total_balance,
        ]);
    }

    /**
     * Calcula payout usando x_por_mina e x_a_cada_5.
     * Ajuste conforme sua regra exata.
     */
    protected function calculatePayout(float $betAmount, int $starsRevealed, GameConfig $config): float
    {
        // Exemplo:
        // payout = betAmount
        //         + (betAmount * x_por_mina * starsRevealed)
        //         + (betAmount * x_a_cada_5 * floor(starsRevealed / 5))
        $payout = $betAmount
            + ($betAmount * $config->x_por_mina * $starsRevealed)
            + ($betAmount * $config->x_a_cada_5 * floor($starsRevealed / 5));

        return round($payout, 2);
    }

    /**
     * Verifica se atingiu a meta e alterna modo se necessário.
     */
    /**
     * Verifica se atingiu a meta e alterna o modo.
     */
    protected function checkModeSwitch(GameConfig $config)
    {
        if ($config->modo_atual === 'arrecadacao') {
            if ($config->total_arrecadado >= $config->meta_arrecadacao) {
                $config->total_arrecadado = 0;
                $config->modo_atual = 'distribuicao';
                $config->start_cycle_at = now();
            }
        } elseif ($config->modo_atual === 'distribuicao') {
            $metaDistribuicao = $config->meta_arrecadacao * ($config->percentual_distribuicao / 100);
            if ($config->total_distribuido >= $metaDistribuicao) {
                $config->total_distribuido = 0;
                $config->modo_atual = 'arrecadacao';
                $config->start_cycle_at = now();
            }
        }
        $config->save();
    }
}
