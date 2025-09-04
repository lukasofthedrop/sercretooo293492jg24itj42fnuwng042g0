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
        // USUÁRIO QUE MAIS DEPOSITOU
        $topDepositor = DB::table('deposits')
            ->join('users', 'users.id', '=', 'deposits.user_id')
            ->select('users.name', 'users.email', DB::raw('SUM(deposits.amount) as total_deposited'))
            ->where('deposits.status', 1)
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_deposited')
            ->first();

        // USUÁRIO QUE MAIS PERDEU (diferença entre apostas e ganhos)
        $topLoser = DB::table('orders as bets')
            ->join('users', 'users.id', '=', 'bets.user_id')
            ->leftJoin(DB::raw('(SELECT user_id, SUM(amount) as total_wins FROM orders WHERE type = "win" GROUP BY user_id) as wins'), 'wins.user_id', '=', 'bets.user_id')
            ->select('users.name', 'users.email', 
                DB::raw('SUM(bets.amount) as total_bets'),
                DB::raw('COALESCE(wins.total_wins, 0) as total_wins'),
                DB::raw('SUM(bets.amount) - COALESCE(wins.total_wins, 0) as net_loss'))
            ->where('bets.type', 'bet')
            ->groupBy('users.id', 'users.name', 'users.email', 'wins.total_wins')
            ->orderByDesc('net_loss')
            ->first();

        // USUÁRIO QUE MAIS TROUXE COMISSÕES
        $topAffiliateCommission = DB::table('affiliate_histories')
            ->join('users', 'users.id', '=', 'affiliate_histories.inviter')
            ->select('users.name', 'users.email', DB::raw('SUM(affiliate_histories.commission_paid) as total_commission'))
            ->where('affiliate_histories.status', 1)
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_commission')
            ->first();

        // USUÁRIO QUE MAIS APOSTOU (volume total)
        $topBetter = DB::table('orders')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->select('users.name', 'users.email', DB::raw('SUM(orders.amount) as total_bet'))
            ->where('orders.type', 'bet')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_bet')
            ->first();

        // USUÁRIO COM MAIS GANHOS
        $topWinner = DB::table('orders')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->select('users.name', 'users.email', DB::raw('SUM(orders.amount) as total_wins'))
            ->where('orders.type', 'win')
            ->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_wins')
            ->first();

        return [
            Stat::make('TOP DEPOSITADOR', $topDepositor ? substr($topDepositor->name, 0, 15) . '...' : 'Aguardando Atividade')
                ->description($topDepositor ? 'R$ ' . number_format($topDepositor->total_deposited, 2, ',', '.') : 'Sistema monitorando depósitos')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart([5, 25, 50, 80, 90, 95, 100])
                ->chartColor('rgba(0, 255, 65, 1.0)'), // Verde Matrix

            Stat::make('MAIOR PERDEDOR', $topLoser ? substr($topLoser->name, 0, 15) . '...' : 'Aguardando Atividade')
                ->description($topLoser ? 'R$ ' . number_format($topLoser->net_loss, 2, ',', '.') . ' perdidos' : 'Sistema analisando perdas')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->chart([100, 85, 75, 60, 40, 25, 10])
                ->chartColor('rgba(239, 68, 68, 1.0)'), // Vermelho claro

            Stat::make('TOP AFILIADO', $topAffiliateCommission ? substr($topAffiliateCommission->name, 0, 15) . '...' : 'Aguardando Atividade')
                ->description($topAffiliateCommission ? 'R$ ' . number_format($topAffiliateCommission->total_commission, 2, ',', '.') . ' comissões' : 'Sistema rastreando afiliados')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('warning')
                ->chart([3, 12, 35, 60, 75, 85, 90])
                ->chartColor('rgba(255, 212, 59, 1.0)'), // Amarelo dourado

            Stat::make('MAIOR APOSTADOR', $topBetter ? substr($topBetter->name, 0, 15) . '...' : 'Aguardando Atividade')
                ->description($topBetter ? 'R$ ' . number_format($topBetter->total_bet, 2, ',', '.') . ' apostado' : 'Sistema preparado para apostas')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('info')
                ->chart([15, 45, 70, 85, 95, 100, 98])
                ->chartColor('rgba(77, 171, 247, 1.0)'), // Azul claro

            Stat::make('MAIOR GANHADOR', $topWinner ? substr($topWinner->name, 0, 15) . '...' : 'Aguardando Atividade')
                ->description($topWinner ? 'R$ ' . number_format($topWinner->total_wins, 2, ',', '.') . ' ganho' : 'Sistema monitorando ganhos')
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