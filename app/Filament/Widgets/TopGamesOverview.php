<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Game;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class TopGamesOverview extends BaseWidget
{
    protected static ?int $sort = 3;
    protected static ?string $pollingInterval = '30s';
    protected static bool $isLazy = true;

    /**
     * @return array|Stat[]
     */
    protected function getStats(): array
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $monthStart = Carbon::now()->startOfMonth();

        // TOP 5 JOGOS MAIS JOGADOS (geral)
        $topGames = DB::table('orders')
            ->select('game', DB::raw('COUNT(*) as plays'))
            ->where('type', 'bet')
            ->groupBy('game')
            ->orderByDesc('plays')
            ->limit(5)
            ->get();

        // JOGO MAIS JOGADO HOJE
        $topGameToday = DB::table('orders')
            ->select('game', DB::raw('COUNT(*) as plays'))
            ->where('type', 'bet')
            ->whereDate('created_at', $today)
            ->groupBy('game')
            ->orderByDesc('plays')
            ->first();

        // JOGO MAIS JOGADO ESTA SEMANA
        $topGameWeek = DB::table('orders')
            ->select('game', DB::raw('COUNT(*) as plays'))
            ->where('type', 'bet')
            ->where('created_at', '>=', $weekStart)
            ->groupBy('game')
            ->orderByDesc('plays')
            ->first();

        // JOGO MAIS JOGADO ESTE MÊS
        $topGameMonth = DB::table('orders')
            ->select('game', DB::raw('COUNT(*) as plays'))
            ->where('type', 'bet')
            ->where('created_at', '>=', $monthStart)
            ->groupBy('game')
            ->orderByDesc('plays')
            ->first();

        // TOTAL DE APOSTAS HOJE
        $totalBetsToday = Order::where('type', 'bet')
            ->whereDate('created_at', $today)
            ->count();

        return [
            Stat::make('JOGO MAIS JOGADO HOJE', $topGameToday ? substr($topGameToday->game, 0, 20) . '...' : 'Aguardando Dados')
                ->description($topGameToday ? $topGameToday->plays . ' apostas hoje' : 'Sistema aguardando primeira aposta')
                ->descriptionIcon('heroicon-m-play-circle')
                ->color('success')
                ->chart([10, 30, 60, 85, 95, 100, 90])
                ->chartColor('rgba(0, 255, 65, 1.0)'), // Verde Matrix

            Stat::make('JOGO MAIS JOGADO SEMANA', $topGameWeek ? substr($topGameWeek->game, 0, 20) . '...' : 'Aguardando Dados')
                ->description($topGameWeek ? $topGameWeek->plays . ' apostas esta semana' : 'Sistema preparado para análise')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success')
                ->chart([5, 20, 45, 70, 80, 90, 95])
                ->chartColor('rgba(77, 171, 247, 1.0)'), // Azul claro

            Stat::make('JOGO MAIS JOGADO MÊS', $topGameMonth ? substr($topGameMonth->game, 0, 20) . '...' : 'Aguardando Dados')
                ->description($topGameMonth ? $topGameMonth->plays . ' apostas este mês' : 'Sistema monitorando tendências')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success')
                ->chart([8, 25, 50, 75, 85, 92, 100])
                ->chartColor('rgba(38, 208, 206, 1.0)'), // Ciano

            Stat::make('TOTAL APOSTAS HOJE', $totalBetsToday ?: '0')
                ->description('Apostas realizadas hoje')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('blue')
                ->chart([20, 35, 50, 65, 80, 85, 90])
                ->chartColor('rgba(255, 107, 53, 1.0)'), // Laranja vibrante

            // TOP GAME OVERALL com detalhes
            Stat::make('JOGO MAIS POPULAR', $topGames->first() ? substr($topGames->first()->game, 0, 18) . '...' : 'Sistema Pronto')
                ->description($topGames->first() ? number_format($topGames->first()->plays) . ' apostas totais' : 'Dashboard inteligente ativado')
                ->descriptionIcon('heroicon-m-star')
                ->color('warning')
                ->chart([15, 40, 70, 90, 95, 98, 100])
                ->chartColor('rgba(255, 212, 59, 1.0)'), // Amarelo dourado
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