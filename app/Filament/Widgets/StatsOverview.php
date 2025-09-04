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
            Stat::make('TOTAL DE CADASTROS', number_format($totalUsers, 0, ',', '.'))
                ->description(new HtmlString('<strong>'.$totalUsers.'</strong> usuários registrados • <span style="color: #4dabf7">Sistema ativo</span>'))
                ->descriptionIcon('heroicon-o-users')
                ->color('blue')
                ->chart([2, 8, 20, 45, 65, 80, 95])
                ->chartColor('rgba(77, 171, 247, 1.0)'), // Azul claro para cadastros
            
            Stat::make('TOTAL DEPOSITADO HOJE', \Helper::amountFormatDecimal($totalDepositedToday)) 
                ->description(new HtmlString($totalDepositedToday > 0 ? '💰 <strong>Recebimentos processados</strong>' : '⏳ <span style="color: #ffd43b">Aguardando depósitos hoje</span>'))
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('green')
                ->chart([30, 50, 40, 60, 80, 70, 90])
                ->chartColor('rgba(0, 255, 65, 1.0)'), // Verde Matrix para depósitos
            
            Stat::make('TOTAL DE SAQUES HOJE', \Helper::amountFormatDecimal($totalsacadoToday))
                ->description(new HtmlString($totalsacadoToday > 0 ? '💳 <strong>Saques processados</strong>' : '🔒 <span style="color: #26d0ce">Sem saques hoje</span>'))
                ->descriptionIcon('heroicon-o-arrow-down-circle')
                ->color('red')
                ->chart([40, 30, 50, 60, 70, 90, 100])
                ->chartColor('rgba(255, 107, 53, 1.0)'), // Laranja vibrante para saques

            Stat::make('SALDO TOTAL DOS PLAYERS', \Helper::amountFormatDecimal($saldodosplayers))
                ->description(new HtmlString($saldodosplayers > 0 ? '💳 <strong>Saldo disponível nas carteiras</strong>' : '💰 <span style="color: #ffd43b">Aguardando primeiros depósitos</span>'))
                ->descriptionIcon('heroicon-o-wallet')
                ->color('purple')
                ->chart([15, 30, 25, 40, 35, 50, 45])
                ->chartColor('rgba(255, 212, 59, 1.0)'), // Amarelo dourado para saldo players

            Stat::make('SALDO TOTAL DOS AFILIADOS', \Helper::amountFormatDecimal($totalReferRewardsLast7Days))
                ->description(new HtmlString($totalReferRewardsLast7Days > 0 ? '🤝 <strong>Comissões acumuladas</strong>' : '🔄 <span style="color: #26d0ce">Sistema de afiliação ativo</span>'))
                ->descriptionIcon('heroicon-o-users')
                ->color('yellow')
                ->chart([5, 15, 10, 20, 25, 30, 35])
                ->chartColor('rgba(38, 208, 206, 1.0)'), // Ciano para afiliados
            
            Stat::make('DEPOSITARAM 1 VEZ', $numberOfUsersWithSingleDeposit)
                ->description(new HtmlString($numberOfUsersWithSingleDeposit > 0 ? '🥉 <strong>Usuários iniciantes</strong> • Potencial conversão' : '⭐ <span style="color: #9c88ff">Aguardando primeiros depósitos</span>'))
                ->descriptionIcon('heroicon-o-user')
                ->color('orange')
                ->chart([30, 45, 55, 60, 65, 70, 75])
                ->chartColor('rgba(156, 136, 255, 1.0)'), // Roxo claro para 1 depósito
            
            Stat::make('DEPOSITARAM 2 VEZES', $numberOfUsersWithTwoDeposits)
                ->description(new HtmlString($numberOfUsersWithTwoDeposits > 0 ? '🥈 <strong>Usuários engajados</strong> • Bom sinal' : '🔄 <span style="color: #ff8cc8">Sistema monitorando reengajamento</span>'))
                ->descriptionIcon('heroicon-o-user-plus')
                ->color('pink')
                ->chart([20, 30, 25, 35, 45, 50, 55])
                ->chartColor('rgba(255, 140, 200, 1.0)'), // Rosa suave para 2 depósitos
            
            Stat::make('DEPOSITARAM 3 VEZES', $numberOfUsersWithThreeDeposits)
                ->description(new HtmlString($numberOfUsersWithThreeDeposits > 0 ? '🥇 <strong>Usuários fiéis</strong> • Alta retenção' : '🎯 <span style="color: #9333ea">Aguardando usuários VIP</span>'))
                ->descriptionIcon('heroicon-o-star')
                ->color('indigo')
                ->chart([45, 50, 55, 60, 65, 70, 80])
                ->chartColor('rgba(147, 51, 234, 1.0)'), // Roxo vibrante para 3 depósitos

            Stat::make('DEPOSITARAM 4+ VEZES', $numberOfUsersWithFourOrMoreDeposits)
                ->description(new HtmlString($numberOfUsersWithFourOrMoreDeposits > 0 ? '👑 <strong>Usuários VIP</strong> • Máxima fidelidade' : '💎 <span style="color: #ec4899">Sistema preparado para VIPs</span>'))
                ->descriptionIcon('heroicon-o-trophy')
                ->color('teal')
                ->chart([25, 35, 30, 40, 45, 55, 60])
                ->chartColor('rgba(236, 72, 153, 1.0)'), // Pink para 4+ depósitos
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
