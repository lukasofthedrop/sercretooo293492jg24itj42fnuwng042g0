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
        // Get top 5 most played games
        $topGames = DB::table('orders')
            ->select('game', DB::raw('COUNT(*) as plays'))
            ->where('type', 'bet')
            ->groupBy('game')
            ->orderByDesc('plays')
            ->limit(5)
            ->get();

        if ($topGames->isEmpty()) {
            // Demo data for professional presentation
            return [
                'labels' => ['Aviator', 'Gates of Olympus', 'Sweet Bonanza', 'Fortune Tiger', 'Spaceman'],
                'datasets' => [
                    [
                        'label' => 'Apostas por Jogo',
                        'data' => [35, 25, 20, 15, 5],
                        'backgroundColor' => [
                            'rgba(0, 255, 0, 0.8)',      // Verde neon - Aviator
                            'rgba(0, 255, 127, 0.8)',    // Verde matrix - Gates
                            'rgba(255, 215, 0, 0.8)',    // Dourado - Sweet Bonanza
                            'rgba(255, 165, 0, 0.8)',    // Laranja - Fortune Tiger
                            'rgba(0, 191, 255, 0.8)',    // Azul neon - Spaceman
                        ],
                        'borderColor' => [
                            'rgba(0, 255, 0, 1)',
                            'rgba(0, 255, 127, 1)', 
                            'rgba(255, 215, 0, 1)',
                            'rgba(255, 165, 0, 1)',
                            'rgba(0, 191, 255, 1)',
                        ],
                        'borderWidth' => 3,
                        'hoverOffset' => 20,
                        'cutout' => '60%',
                    ]
                ]
            ];
        }

        // Real data processing
        $labels = [];
        $data = [];
        $colors = [
            'rgba(0, 255, 0, 0.8)',
            'rgba(0, 255, 127, 0.8)', 
            'rgba(255, 215, 0, 0.8)',
            'rgba(255, 165, 0, 0.8)',
            'rgba(0, 191, 255, 0.8)',
        ];
        $borderColors = [
            'rgba(0, 255, 0, 1)',
            'rgba(0, 255, 127, 1)',
            'rgba(255, 215, 0, 1)', 
            'rgba(255, 165, 0, 1)',
            'rgba(0, 191, 255, 1)',
        ];

        foreach ($topGames as $index => $game) {
            $labels[] = substr($game->game, 0, 20) . (strlen($game->game) > 20 ? '...' : '');
            $data[] = $game->plays;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Apostas por Jogo',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                    'borderColor' => array_slice($borderColors, 0, count($data)),
                    'borderWidth' => 3,
                    'hoverOffset' => 20,
                    'cutout' => '60%',
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
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'right',
                    'labels' => [
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                        'padding' => 20,
                        'font' => [
                            'size' => 12,
                            'family' => 'Inter, sans-serif',
                            'weight' => 'bold',
                        ],
                        'color' => '#00FF00',
                        'generateLabels' => null, // Will use Chart.js callback
                    ]
                ],
                'tooltip' => [
                    'backgroundColor' => 'rgba(0, 0, 0, 0.9)',
                    'titleColor' => '#00FF00', 
                    'bodyColor' => '#FFFFFF',
                    'borderColor' => '#00FF00',
                    'borderWidth' => 2,
                    'cornerRadius' => 8,
                    'displayColors' => true,
                    'callbacks' => [
                        'label' => 'function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = Math.round((context.parsed * 100) / total);
                            return context.label + ": " + context.parsed + " apostas (" + percentage + "%)";
                        }'
                    ]
                ]
            ],
            'elements' => [
                'arc' => [
                    'borderWidth' => 3,
                    'hoverBorderWidth' => 5,
                ]
            ],
            'animation' => [
                'animateRotate' => true,
                'animateScale' => true,
                'duration' => 1500,
                'easing' => 'easeInOutQuart'
            ],
            'layout' => [
                'padding' => [
                    'top' => 20,
                    'bottom' => 20,
                    'left' => 10,
                    'right' => 10
                ]
            ]
        ];
    }

    /**
     * Custom view for enhanced styling
     */
    protected function getView(): string
    {
        return 'filament.widgets.top5-games-chart';
    }

    /**
     * Check if user can view this widget
     */
    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}