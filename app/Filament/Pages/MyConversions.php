<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class MyConversions extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Minhas Conversões';
    protected static ?string $title = 'Minhas Conversões';
    protected static ?string $slug = 'minhas-conversoes';
    protected static ?int $navigationSort = 2;
    
    protected static string $view = 'filament.pages.my-conversions';
    
    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && !$user->hasRole('admin');
    }
    
    protected function getViewData(): array
    {
        $user = auth()->user();
        
        // Buscar conversões do afiliado atual
        $conversions = DB::table('orders')
            ->join('users as referred', 'orders.user_id', '=', 'referred.id')
            ->where('referred.inviter', $user->inviter_code)
            ->where('orders.status', 1) // Apenas ordens pagas
            ->select(
                'orders.id',
                'referred.name as user_name',
                'referred.email as user_email',
                'orders.amount as valor_deposito',
                'orders.created_at',
                DB::raw('(orders.amount * 0.40) as comissao_display'), // 40% fake
                DB::raw('(orders.amount * 0.05) as comissao_real') // 5% real
            )
            ->orderBy('orders.created_at', 'desc')
            ->limit(100)
            ->get();
        
        // Calcular totais
        $totalDepositos = $conversions->sum('valor_deposito');
        $totalComissaoDisplay = $conversions->sum('comissao_display');
        $totalComissaoReal = $conversions->sum('comissao_real');
        
        return [
            'conversions' => $conversions,
            'totalDepositos' => $totalDepositos,
            'totalComissaoDisplay' => $totalComissaoDisplay,
            'totalComissaoReal' => $totalComissaoReal,
            'userName' => $user->name,
            'userCode' => $user->inviter_code
        ];
    }
}