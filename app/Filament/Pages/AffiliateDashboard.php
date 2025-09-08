<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;
use App\Models\AffiliateSettings;
use App\Models\Deposit;
use App\Models\Withdrawal;
use Carbon\Carbon;

class AffiliateDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Minha Dashboard Afiliado';
    protected static ?string $navigationGroup = 'GESTÃO DE AFILIADOS';
    protected static ?int $navigationSort = 4;
    protected static ?string $slug = 'minha-dashboard';
    protected static ?string $title = 'Dashboard do Afiliado';
    
    protected static string $view = 'filament.pages.affiliate-dashboard';

    public static function canAccess(): bool
    {
        // Todos os usuários logados podem acessar sua própria dashboard
        return auth()->check();
    }
    
    public static function shouldRegisterNavigation(): bool
    {
        // Sempre mostrar no menu para usuários logados
        return auth()->check();
    }

    public function mount(): void
    {
        $user = auth()->user();
        
        // Se não tem código de afiliado, gera um
        if (!$user->inviter_code) {
            $code = $this->generateCode();
            if (!empty($code)) {
                $user->inviter_code = $code;
                $user->save();
                
                // Adiciona role de afiliado
                \DB::table('model_has_roles')->updateOrInsert(
                    [
                        'role_id' => 2,
                        'model_type' => 'App\Models\User',
                        'model_id' => $user->id,
                    ],
                );
            }
        }
    }
    
    private function generateCode()
    {
        $code = 'AFF' . date('Y') . strtoupper(substr(uniqid(), -6));
        
        // Verifica se código já existe
        while (User::where('inviter_code', $code)->exists()) {
            $code = 'AFF' . date('Y') . strtoupper(substr(uniqid(), -6));
        }
        
        return $code;
    }

    public function getViewData(): array
    {
        $user = auth()->user();
        $settings = AffiliateSettings::getOrCreateForUser($user->id);
        
        // Verifica último saque do afiliado (para controle semanal)
        $lastWithdraw = \DB::table('affiliate_withdraws')
            ->where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->orderBy('created_at', 'desc')
            ->first();
            
        $canWithdraw = !$lastWithdraw; // Pode sacar se não sacou nos últimos 7 dias
        $nextWithdrawDate = $lastWithdraw 
            ? Carbon::parse($lastWithdraw->created_at)->addDays(7)->format('d/m/Y')
            : null;
        
        // Busca indicados
        $referred = User::where('inviter', $user->id)->get();
        $referredCount = $referred->count();
        
        // Calcula indicados ativos (fizeram depósito nos últimos 30 dias)
        $activeReferred = $referred->filter(function($ref) {
            return Deposit::where('user_id', $ref->id)
                ->where('status', 1)
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->exists();
        })->count();
        
        // Calcula NGR do mês atual
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        
        $referredIds = $referred->pluck('id');
        
        $monthDeposits = Deposit::whereIn('user_id', $referredIds)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', 1)
            ->sum('amount');
            
        $monthWithdrawals = Withdrawal::whereIn('user_id', $referredIds)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', 1)
            ->sum('amount');
            
        $monthNGR = $monthDeposits - $monthWithdrawals;
        
        // Calcula total ganho (baseado no FAKE 40%)
        $totalEarned = $user->wallet->refer_rewards ?? 0;
        
        // Dados dos últimos 6 meses para gráfico
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();
            
            $deposits = Deposit::whereIn('user_id', $referredIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 1)
                ->sum('amount');
                
            $withdrawals = Withdrawal::whereIn('user_id', $referredIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 1)
                ->sum('amount');
                
            $ngr = $deposits - $withdrawals;
            
            $monthlyData[] = [
                'month' => $date->format('M/Y'),
                'deposits' => $deposits,
                'withdrawals' => $withdrawals,
                'ngr' => $ngr,
                // Mostra comissão FAKE de 40%
                'commission' => $ngr * 0.40
            ];
        }
        
        // Lista de indicados recentes
        $recentReferred = $referred->take(10)->map(function($ref) {
            $totalDeposited = Deposit::where('user_id', $ref->id)
                ->where('status', 1)
                ->sum('amount');
                
            $isActive = Deposit::where('user_id', $ref->id)
                ->where('status', 1)
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->exists();
                
            return [
                'name' => $ref->name,
                'email' => $ref->email,
                'created_at' => is_string($ref->created_at) ? Carbon::parse($ref->created_at)->format('d/m/Y') : $ref->created_at->format('d/m/Y'),
                'is_active' => $isActive,
                'total_deposited' => $totalDeposited,
                // Mostra comissão FAKE de 40%
                'commission_generated' => $totalDeposited * 0.40
            ];
        });
        
        return [
            'user' => $user,
            'affiliate_code' => $user->inviter_code,
            'invite_link' => url('/register?code=' . $user->inviter_code),
            'total_referred' => $referredCount,
            'active_referred' => $activeReferred,
            'available_balance' => $user->wallet->refer_rewards ?? 0,
            'total_earned' => $totalEarned,
            'month_ngr' => $monthNGR,
            // SEMPRE mostra 40% como RevShare (FAKE)
            'revshare_percentage' => 40,
            'monthly_data' => $monthlyData,
            'recent_referred' => $recentReferred,
            'settings' => $settings,
            // Controle de saque semanal
            'can_withdraw' => $canWithdraw,
            'next_withdraw_date' => $nextWithdrawDate,
            'last_withdraw' => $lastWithdraw
        ];
    }
}