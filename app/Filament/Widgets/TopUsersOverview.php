<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\User;
use App\Models\Deposit;
use App\Models\AffiliateHistory;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Cache;

class TopUsersOverview extends BaseWidget
{
    protected static ?int $sort = 4;
    protected static ?string $pollingInterval = '60s';
    protected static bool $isLazy = true;

    /**
     * @return array|Stat[]
     */
    protected function getStats(): array
    {
        // Cache de rankings de usuários (30 minutos)
        $userRankings = Cache::remember('top_users_rankings', 1800, function () {
            return [
                'top_depositor' => DB::table('deposits')
                    ->join('users', 'users.id', '=', 'deposits.user_id')
                    ->select('users.name', 'users.email', DB::raw('SUM(deposits.amount) as total_deposited'))
                    ->where('deposits.status', 1)
                    ->groupBy('users.id', 'users.name', 'users.email')
                    ->orderByDesc('total_deposited')
                    ->first(),
                
                'top_loser' => DB::table('orders')
                    ->join('users', 'users.id', '=', 'orders.user_id')
                    ->select('users.name', 'users.email', 'users.id',
                        DB::raw('SUM(CASE WHEN orders.type = "bet" THEN orders.amount ELSE 0 END) as total_bets'),
                        DB::raw('SUM(CASE WHEN orders.type = "win" THEN orders.amount ELSE 0 END) as total_wins'),
                        DB::raw('SUM(CASE WHEN orders.type = "bet" THEN orders.amount ELSE 0 END) - SUM(CASE WHEN orders.type = "win" THEN orders.amount ELSE 0 END) as net_loss'))
                    ->whereIn('orders.type', ['bet', 'win'])
                    ->groupBy('users.id', 'users.name', 'users.email')
                    ->having('total_bets', '>', 0)
                    ->orderByDesc('net_loss')
                    ->first(),
                    
                'top_affiliate' => DB::table('affiliate_histories')
                    ->join('users', 'users.id', '=', 'affiliate_histories.inviter')
                    ->select('users.name', 'users.email', DB::raw('SUM(affiliate_histories.commission_paid) as total_commission'))
                    ->where('affiliate_histories.status', 1)
                    ->groupBy('users.id', 'users.name', 'users.email')
                    ->orderByDesc('total_commission')
                    ->first(),
                    
                'top_better' => DB::table('orders')
                    ->join('users', 'users.id', '=', 'orders.user_id')
                    ->select('users.name', 'users.email', DB::raw('SUM(orders.amount) as total_bet'))
                    ->where('orders.type', 'bet')
                    ->groupBy('users.id', 'users.name', 'users.email')
                    ->orderByDesc('total_bet')
                    ->first(),
                    
                'top_winner' => DB::table('orders')
                    ->join('users', 'users.id', '=', 'orders.user_id')
                    ->select('users.name', 'users.email', DB::raw('SUM(orders.amount) as total_wins'))
                    ->where('orders.type', 'win')
                    ->groupBy('users.id', 'users.name', 'users.email')
                    ->orderByDesc('total_wins')
                    ->first()
            ];
        });

        $topDepositor = $userRankings['top_depositor'];
        $topLoser = $userRankings['top_loser'];
        $topAffiliateCommission = $userRankings['top_affiliate'];
        $topBetter = $userRankings['top_better'];
        $topWinner = $userRankings['top_winner'];

        return [
            Stat::make('TOP DEPOSITADOR', $topDepositor ? substr($topDepositor->name, 0, 14) . '...' : 'Aguardando Atividade')
                ->description(new HtmlString($topDepositor ? '💰 <strong>R$ ' . number_format($topDepositor->total_deposited, 2, ',', '.') . '</strong> • Usuário VIP' : '🎯 <span style="color: #00ff41">Sistema monitorando depósitos</span>'))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([5, 25, 50, 80, 90, 95, 100])
                ->chartColor('rgba(0, 255, 65, 1.0)'), // Verde Matrix

            Stat::make('MAIOR PERDEDOR', $topLoser ? substr($topLoser->name, 0, 14) . '...' : 'Aguardando Atividade')
                ->description(new HtmlString($topLoser ? '📉 <strong>R$ ' . number_format($topLoser->net_loss, 2, ',', '.') . '</strong> • Diferencial casa' : '📊 <span style="color: #ef4444">Sistema analisando performance</span>'))
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->chart([100, 85, 75, 60, 40, 25, 10])
                ->chartColor('rgba(239, 68, 68, 1.0)'), // Vermelho claro

            Stat::make('TOP AFILIADO', $topAffiliateCommission ? substr($topAffiliateCommission->name, 0, 14) . '...' : 'Aguardando Atividade')
                ->description(new HtmlString($topAffiliateCommission ? '🤝 <strong>R$ ' . number_format($topAffiliateCommission->total_commission, 2, ',', '.') . '</strong> • Melhor parceiro' : '🔗 <span style="color: #ffd43b">Sistema rastreando afiliados</span>'))
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning')
                ->chart([3, 12, 35, 60, 75, 85, 90])
                ->chartColor('rgba(255, 212, 59, 1.0)'), // Amarelo dourado

            Stat::make('MAIOR APOSTADOR', $topBetter ? substr($topBetter->name, 0, 14) . '...' : 'Aguardando Atividade')
                ->description(new HtmlString($topBetter ? '🎲 <strong>R$ ' . number_format($topBetter->total_bet, 2, ',', '.') . '</strong> • Volume total' : '🎯 <span style="color: #4dabf7">Sistema preparado para apostas</span>'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info')
                ->chart([15, 45, 70, 85, 95, 100, 98])
                ->chartColor('rgba(77, 171, 247, 1.0)'), // Azul claro

            Stat::make('MAIOR GANHADOR', $topWinner ? substr($topWinner->name, 0, 14) . '...' : 'Aguardando Atividade')
                ->description(new HtmlString($topWinner ? '🏆 <strong>R$ ' . number_format($topWinner->total_wins, 2, ',', '.') . '</strong> • Maior sorte' : '🍀 <span style="color: #ff8cc8">Sistema monitorando ganhos</span>'))
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success')
                ->chart([10, 35, 65, 80, 90, 95, 100])
                ->chartColor('rgba(255, 140, 200, 1.0)'), // Rosa suave
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