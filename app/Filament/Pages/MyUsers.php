<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class MyUsers extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Usuários Indicados';
    protected static ?string $title = 'Meus Usuários Indicados';
    protected static ?string $slug = 'usuarios-indicados';
    protected static ?int $navigationSort = 3;
    
    protected static string $view = 'filament.pages.my-users';
    
    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && !$user->hasRole('admin');
    }
    
    protected function getViewData(): array
    {
        $user = auth()->user();
        
        // Buscar usuários indicados pelo afiliado atual
        $referredUsers = User::where('inviter', $user->inviter_code)
            ->with(['wallet'])
            ->select(
                'id',
                'name',
                'email',
                'created_at',
                'phone',
                DB::raw('(SELECT SUM(amount) FROM orders WHERE user_id = users.id AND status = 1) as total_depositos'),
                DB::raw('(SELECT COUNT(*) FROM orders WHERE user_id = users.id AND status = 1) as qtd_depositos')
            )
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Calcular estatísticas
        $totalUsers = $referredUsers->count();
        $activeUsers = $referredUsers->filter(function($u) {
            return $u->total_depositos > 0;
        })->count();
        $totalDeposits = $referredUsers->sum('total_depositos');
        
        // Taxa de conversão
        $conversionRate = $totalUsers > 0 ? ($activeUsers / $totalUsers) * 100 : 0;
        
        return [
            'referredUsers' => $referredUsers,
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'totalDeposits' => $totalDeposits,
            'conversionRate' => $conversionRate,
            'userName' => $user->name,
            'userCode' => $user->inviter_code
        ];
    }
}