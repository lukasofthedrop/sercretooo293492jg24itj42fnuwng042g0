<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Game;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

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

        // Query otimizada combinada para todos os períodos
        $gameStats = DB::table('orders')
            ->select('game',
                DB::raw('COUNT(*) as total_plays'),
                DB::raw('SUM(CASE WHEN DATE(created_at) = ? THEN 1 ELSE 0 END) as today_plays', [$today->toDateString()]),
                DB::raw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as week_plays', [$weekStart->toDateTimeString()]),
                DB::raw('SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as month_plays', [$monthStart->toDateTimeString()])
            )
            ->where('type', 'bet')
            ->groupBy('game')
            ->orderByDesc('total_plays')
            ->limit(10)
            ->get();

        // Extrair top games por período
        $topGames = $gameStats->take(5);
        $topGameToday = $gameStats->sortByDesc('today_plays')->filter(function($game) { 
            return $game->today_plays > 0; 
        })->first();
        $topGameWeek = $gameStats->sortByDesc('week_plays')->filter(function($game) { 
            return $game->week_plays > 0; 
        })->first();
        $topGameMonth = $gameStats->sortByDesc('month_plays')->filter(function($game) { 
            return $game->month_plays > 0; 
        })->first();

        // Total de apostas hoje (otimizado)
        $totalBetsToday = DB::table('orders')
            ->where('type', 'bet')
            ->whereDate('created_at', $today)
            ->count();

        return [
            Stat::make('TOP JOGO HOJE', $topGameToday ? substr($topGameToday->game, 0, 18) . '...' : 'Aguardando Atividade')
                ->description(new HtmlString($topGameToday ? '🔥 <strong>'.$topGameToday->today_plays.' apostas</strong> • Liderando hoje' : '🎯 <span style="color: #00ff41">Sistema monitorando jogos</span>'))
                ->descriptionIcon('heroicon-m-fire')
                ->color('success')
                ->chart([10, 30, 60, 85, 95, 100, 90])
                ->chartColor('rgba(0, 255, 65, 1.0)'), // Verde Matrix

            Stat::make('TOP JOGO SEMANA', $topGameWeek ? substr($topGameWeek->game, 0, 18) . '...' : 'Aguardando Atividade')
                ->description(new HtmlString($topGameWeek ? '⭐ <strong>'.$topGameWeek->week_plays.' apostas</strong> • Tendência semanal' : '📊 <span style="color: #4dabf7">Analytics preparado</span>'))
                ->descriptionIcon('heroicon-m-chart-bar-square')
                ->color('success')
                ->chart([5, 20, 45, 70, 80, 90, 95])
                ->chartColor('rgba(77, 171, 247, 1.0)'), // Azul claro

            Stat::make('TOP JOGO MÊS', $topGameMonth ? substr($topGameMonth->game, 0, 18) . '...' : 'Aguardando Atividade')
                ->description(new HtmlString($topGameMonth ? '👑 <strong>'.$topGameMonth->month_plays.' apostas</strong> • Campeão mensal' : '📈 <span style="color: #26d0ce">Rastreamento ativo</span>'))
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success')
                ->chart([8, 25, 50, 75, 85, 92, 100])
                ->chartColor('rgba(38, 208, 206, 1.0)'), // Ciano

            Stat::make('APOSTAS HOJE', number_format($totalBetsToday, 0, ',', '.'))
                ->description(new HtmlString($totalBetsToday > 0 ? '🎲 <strong>Apostas processadas</strong> • Sistema ativo' : '⏱️ <span style="color: #ff6b35">Aguardando primeira aposta</span>'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('blue')
                ->chart([20, 35, 50, 65, 80, 85, 90])
                ->chartColor('rgba(255, 107, 53, 1.0)'), // Laranja vibrante

            Stat::make('JOGO MAIS POPULAR', $topGames->first() ? substr($topGames->first()->game, 0, 16) . '...' : 'Sistema Preparado')
                ->description(new HtmlString($topGames->first() ? '🏆 <strong>'.number_format($topGames->first()->total_plays).' apostas</strong> • Líder absoluto' : '🚀 <span style="color: #ffd43b">Dashboard inteligente ativo</span>'))
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