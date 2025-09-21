<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Cache;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = '15s';
    protected static bool $isLazy = true;

    /*** @return array|Stat[]
     */
    protected function getStats(): array
    {
        $today = Carbon::today();
        $todayKey = $today->format('Y-m-d');

        // Cache de dados financeiros diários (5 minutos)
        $financialData = Cache::remember("stats_financial_{$todayKey}", 300, function () use ($today) {
            return [
                'deposited_today' => DB::table('deposits')
                    ->whereDate('created_at', $today)
                    ->where('status', '1')
                    ->sum('amount'),
                'withdrawn_today' => DB::table('withdrawals')
                    ->whereDate('created_at', $today)
                    ->where('status', '1')
                    ->sum('amount')
            ];
        });

        // Cache de saldos dos players (15 minutos)
        $saldodosplayers = Cache::remember('stats_player_balance', 900, function () {
            return DB::table('wallets')
                ->join('users', 'users.id', '=', 'wallets.user_id')
                ->sum(DB::raw('wallets.balance + wallets.balance_bonus + wallets.balance_withdrawal'));
        });

        // Cache de ganhos de afiliados (15 minutos)
        $totalReferRewardsLast7Days = Cache::remember('stats_affiliate_rewards', 900, function () {
            return Wallet::where('refer_rewards', '>=', 1)->sum('refer_rewards');
        });

        // Cache de estatísticas de depósitos por usuário (1 hora)
        $depositStats = Cache::remember('stats_deposit_counts', 3600, function () {
            return DB::table('deposits')
                ->select(
                    DB::raw('SUM(CASE WHEN deposit_count = 1 THEN 1 ELSE 0 END) as single_deposit'),
                    DB::raw('SUM(CASE WHEN deposit_count = 2 THEN 1 ELSE 0 END) as two_deposits'),
                    DB::raw('SUM(CASE WHEN deposit_count = 3 THEN 1 ELSE 0 END) as three_deposits'),
                    DB::raw('SUM(CASE WHEN deposit_count >= 4 THEN 1 ELSE 0 END) as four_or_more_deposits')
                )
                ->fromSub(
                    DB::table('deposits')
                        ->select('user_id', DB::raw('count(*) as deposit_count'))
                        ->where('status', '1')
                        ->groupBy('user_id'),
                    'deposit_counts'
                )
                ->first();
        });

        // Cache de contagem de usuários (30 minutos)
        $totalUsers = Cache::remember('stats_total_users', 1800, function () {
            return User::count();
        });

        $totalDepositedToday = $financialData['deposited_today'];
        $totalsacadoToday = $financialData['withdrawn_today'];
        $numberOfUsersWithSingleDeposit = $depositStats->single_deposit ?? 0;
        $numberOfUsersWithTwoDeposits = $depositStats->two_deposits ?? 0;
        $numberOfUsersWithThreeDeposits = $depositStats->three_deposits ?? 0;
        $numberOfUsersWithFourOrMoreDeposits = $depositStats->four_or_more_deposits ?? 0;

        return [
            // Métricas Financeiras Principais - Verde LucrativaBet
            Stat::make('DEPÓSITOS HOJE', \Helper::amountFormatDecimal($totalDepositedToday)) 
                ->description($totalDepositedToday > 0 ? '💰 Entrada de capital processada' : '⏳ Aguardando depósitos')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success')
                ->chart([30, 50, 40, 60, 80, 70, 90])
                ->chartColor('#22c55e'), // Verde LucrativaBet
            
            Stat::make('SAQUES HOJE', \Helper::amountFormatDecimal($totalsacadoToday))
                ->description($totalsacadoToday > 0 ? '💳 Saques processados' : '🔒 Sem saques hoje')
                ->descriptionIcon('heroicon-o-arrow-down-circle')
                ->color('danger')
                ->chart([40, 30, 50, 60, 70, 90, 100])
                ->chartColor('#ef4444'), // Vermelho para saques

            Stat::make('SALDO DOS PLAYERS', \Helper::amountFormatDecimal($saldodosplayers))
                ->description('💳 Capital disponível nas carteiras')
                ->descriptionIcon('heroicon-o-wallet')
                ->color('info')
                ->chart([15, 30, 25, 40, 35, 50, 45])
                ->chartColor('#3b82f6'), // Azul para saldo

            // Métricas de Usuários - Tons neutros profissionais
            Stat::make('TOTAL DE CADASTROS', number_format($totalUsers, 0, ',', '.'))
                ->description('👥 Base de usuários registrados')
                ->descriptionIcon('heroicon-o-users')
                ->color('gray')
                ->chart([2, 8, 20, 45, 65, 80, 95])
                ->chartColor('#6b7280'), // Cinza neutro

            Stat::make('COMISSÕES AFILIADOS', \Helper::amountFormatDecimal($totalReferRewardsLast7Days))
                ->description('🤝 Sistema de referência ativo')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('warning')
                ->chart([5, 15, 10, 20, 25, 30, 35])
                ->chartColor('#f59e0b'), // Amarelo para afiliação

            // Métricas de Engajamento - Escala de verde claro
            Stat::make('1º DEPÓSITO', $numberOfUsersWithSingleDeposit)
                ->description('🌱 Usuários iniciantes')
                ->descriptionIcon('heroicon-o-user')
                ->color('success')
                ->chart([30, 45, 55, 60, 65, 70, 75])
                ->chartColor('#4ade80'), // Verde claro
            
            Stat::make('2º DEPÓSITO', $numberOfUsersWithTwoDeposits)
                ->description('📈 Usuários engajados')
                ->descriptionIcon('heroicon-o-user-plus')
                ->color('success')
                ->chart([20, 30, 25, 35, 45, 50, 55])
                ->chartColor('#22c55e'), // Verde principal
            
            Stat::make('3º DEPÓSITO', $numberOfUsersWithThreeDeposits)
                ->description('🎯 Usuários fiéis')
                ->descriptionIcon('heroicon-o-star')
                ->color('success')
                ->chart([45, 50, 55, 60, 65, 70, 80])
                ->chartColor('#15803d'), // Verde escuro

            Stat::make('4+ DEPÓSITOS', $numberOfUsersWithFourOrMoreDeposits)
                ->description('👑 Usuários VIP')
                ->descriptionIcon('heroicon-o-trophy')
                ->color('success')
                ->chart([25, 35, 30, 40, 45, 55, 60])
                ->chartColor('#166534'), // Verde muito escuro - VIP
        ];
    }

    /**
     * @return bool
     */
    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}
