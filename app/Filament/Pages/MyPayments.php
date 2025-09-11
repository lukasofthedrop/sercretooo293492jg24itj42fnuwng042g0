<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class MyPayments extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Histórico de Pagamentos';
    protected static ?string $title = 'Meu Histórico de Pagamentos';
    protected static ?string $slug = 'historico-pagamentos';
    protected static ?int $navigationSort = 4;
    
    protected static string $view = 'filament.pages.my-payments';
    
    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && !$user->hasRole('admin');
    }
    
    protected function getViewData(): array
    {
        $user = auth()->user();
        
        // Buscar pagamentos do afiliado
        $payments = DB::table('affiliate_withdraws')
            ->where('user_id', $user->id)
            ->select(
                'id',
                'amount',
                'amount_display', // valor display (40%)
                'amount_real', // valor real pago (5%)
                'pix_key',
                'pix_type',
                'status',
                'proof',
                'created_at',
                'updated_at'
            )
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Mapear status numéricos para strings
        $payments = $payments->map(function($payment) {
            $statusMap = [
                0 => 'pending',
                1 => 'paid',
                2 => 'rejected'
            ];
            $payment->status = $statusMap[$payment->status] ?? 'pending';
            return $payment;
        });
        
        // Calcular estatísticas
        $totalPago = $payments->where('status', 'paid')->sum('amount_display'); // Display 40%
        $totalPagoReal = $payments->where('status', 'paid')->sum('amount_real'); // Real 5%
        $totalPendente = $payments->where('status', 'pending')->sum('amount');
        $totalRejeitado = $payments->where('status', 'rejected')->sum('amount');
        
        // Buscar saldo disponível para saque
        $disponivel = DB::table('orders')
            ->join('users as referred', 'orders.user_id', '=', 'referred.id')
            ->where('referred.inviter', $user->inviter_code)
            ->where('orders.status', 1)
            ->sum(DB::raw('orders.amount * 0.40')); // 40% display
        
        $totalSacado = $payments->where('status', 'paid')->sum('amount');
        $saldoDisponivel = $disponivel - $totalSacado;
        
        return [
            'payments' => $payments,
            'totalPago' => $totalPago,
            'totalPagoReal' => $totalPagoReal,
            'totalPendente' => $totalPendente,
            'totalRejeitado' => $totalRejeitado,
            'saldoDisponivel' => $saldoDisponivel,
            'userName' => $user->name,
            'userCode' => $user->inviter_code
        ];
    }
}