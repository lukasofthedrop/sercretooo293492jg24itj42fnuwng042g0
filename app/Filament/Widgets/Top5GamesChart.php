<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Game;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class Top5GamesChart extends ChartWidget
{
    protected static ?string $heading = '🎮 TOP 5 JOGOS MAIS JOGADOS';
    protected static ?int $sort = 5;
    protected static ?string $pollingInterval = '30s';
    protected static bool $isLazy = true;

    /**
     * Chart type - using advanced Chart.js doughnut with custom styling
     */
    protected function getType(): string
    {
        return 'doughnut';
    }

    /**
     * Get chart data with professional infographic styling
     */
    protected function getData(): array
    {
        return [
            'labels' => ['Aviator', 'Gates of Olympus', 'Sweet Bonanza', 'Fortune Tiger', 'Spaceman'],
            'datasets' => [
                [
                    'data' => [35, 25, 20, 15, 5],
                    'backgroundColor' => [
                        '#00ff00',
                        '#00ff7f', 
                        '#ffd700',
                        '#ffa500',
                        '#00bfff',
                    ],
                ]
            ]
        ];
    }

    /**
     * Professional Chart.js options for infographic look
     */
    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }

    // /**
    //  * Custom view for enhanced styling
    //  */
    // protected function getView(): string
    // {
    //     return 'filament.widgets.top5-games-chart';
    // }

    /**
     * Check if user can view this widget
     */
    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}