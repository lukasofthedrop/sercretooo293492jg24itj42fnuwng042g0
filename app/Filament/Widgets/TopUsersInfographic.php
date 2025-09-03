<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Deposit;
use App\Models\Order;
use App\Models\AffiliateHistory;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopUsersInfographic extends ChartWidget
{
    protected static ?string $heading = '👑 RANKING PROFISSIONAL DE USUÁRIOS';
    protected static ?int $sort = 6;
    protected static ?string $pollingInterval = '60s';
    protected static bool $isLazy = true;

    /**
     * Chart type - horizontal bar for professional ranking display
     */
    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * Get professional infographic data
     */
    protected function getData(): array
    {
        // Get top performers in different categories
        $topDepositors = $this->getTopDepositors();
        $topBetters = $this->getTopBetters();
        $topAffiliates = $this->getTopAffiliates();

        if (empty($topDepositors) && empty($topBetters) && empty($topAffiliates)) {
            // Professional demo data for visualization
            return [
                'labels' => [
                    'João Silva', 'Maria Santos', 'Pedro Costa', 
                    'Ana Oliveira', 'Carlos Lima'
                ],
                'datasets' => [
                    [
                        'label' => '💰 Total Depositado (R$)',
                        'data' => [15000, 12500, 10800, 9200, 7500],
                        'backgroundColor' => 'rgba(0, 255, 0, 0.8)',
                        'borderColor' => 'rgba(0, 255, 0, 1)',
                        'borderWidth' => 2,
                        'borderRadius' => 8,
                        'borderSkipped' => false,
                    ],
                    [
                        'label' => '🎯 Total Apostado (R$)',
                        'data' => [25000, 18000, 16500, 14200, 11800],
                        'backgroundColor' => 'rgba(0, 255, 127, 0.8)',
                        'borderColor' => 'rgba(0, 255, 127, 1)',
                        'borderWidth' => 2,
                        'borderRadius' => 8,
                        'borderSkipped' => false,
                    ],
                    [
                        'label' => '🤝 Comissões Geradas (R$)',
                        'data' => [850, 650, 520, 380, 280],
                        'backgroundColor' => 'rgba(255, 215, 0, 0.8)',
                        'borderColor' => 'rgba(255, 215, 0, 1)',
                        'borderWidth' => 2,
                        'borderRadius' => 8,
                        'borderSkipped' => false,
                    ]
                ]
            ];
        }

        // Process real data when available
        return $this->processRealData($topDepositors, $topBetters, $topAffiliates);
    }

    private function getTopDepositors()
    {
        return DB::table('deposits')
            ->join('users', 'users.id', '=', 'deposits.user_id')
            ->select('users.name', DB::raw('SUM(deposits.amount) as total'))
            ->where('deposits.status', 1)
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    }

    private function getTopBetters()
    {
        return DB::table('orders')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->select('users.name', DB::raw('SUM(orders.amount) as total'))
            ->where('orders.type', 'bet')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    }

    private function getTopAffiliates()
    {
        return DB::table('affiliate_histories')
            ->join('users', 'users.id', '=', 'affiliate_histories.inviter')
            ->select('users.name', DB::raw('SUM(affiliate_histories.commission_paid) as total'))
            ->where('affiliate_histories.status', 1)
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();
    }

    private function processRealData($depositors, $betters, $affiliates)
    {
        // Implementation for real data processing
        $labels = [];
        $depositData = [];
        $betData = [];
        $affiliateData = [];

        // Combine and process data
        $allUsers = collect($depositors)->merge($betters)->merge($affiliates)
            ->unique('name')->take(5);

        foreach ($allUsers as $user) {
            $labels[] = substr($user->name, 0, 15) . (strlen($user->name) > 15 ? '...' : '');
            $depositData[] = $depositors->where('name', $user->name)->first()->total ?? 0;
            $betData[] = $betters->where('name', $user->name)->first()->total ?? 0;
            $affiliateData[] = $affiliates->where('name', $user->name)->first()->total ?? 0;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => '💰 Total Depositado (R$)',
                    'data' => $depositData,
                    'backgroundColor' => 'rgba(0, 255, 0, 0.8)',
                    'borderColor' => 'rgba(0, 255, 0, 1)',
                    'borderWidth' => 2,
                    'borderRadius' => 8,
                    'borderSkipped' => false,
                ],
                [
                    'label' => '🎯 Total Apostado (R$)',
                    'data' => $betData,
                    'backgroundColor' => 'rgba(0, 255, 127, 0.8)',
                    'borderColor' => 'rgba(0, 255, 127, 1)',
                    'borderWidth' => 2,
                    'borderRadius' => 8,
                    'borderSkipped' => false,
                ],
                [
                    'label' => '🤝 Comissões Geradas (R$)',
                    'data' => $affiliateData,
                    'backgroundColor' => 'rgba(255, 215, 0, 0.8)',
                    'borderColor' => 'rgba(255, 215, 0, 1)',
                    'borderWidth' => 2,
                    'borderRadius' => 8,
                    'borderSkipped' => false,
                ]
            ]
        ];
    }

    /**
     * Professional Chart.js options for ranking infographic
     */
    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y', // Horizontal bars
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                    'labels' => [
                        'usePointStyle' => true,
                        'pointStyle' => 'rectRounded',
                        'padding' => 20,
                        'font' => [
                            'size' => 12,
                            'family' => 'Inter, sans-serif',
                            'weight' => 'bold',
                        ],
                        'color' => '#00FF00',
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
                            return context.dataset.label + ": R$ " + context.parsed.x.toLocaleString("pt-BR", {minimumFractionDigits: 2});
                        }'
                    ]
                ]
            ],
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'color' => 'rgba(0, 255, 0, 0.1)',
                        'borderColor' => 'rgba(0, 255, 0, 0.3)',
                    ],
                    'ticks' => [
                        'color' => '#00FF00',
                        'font' => [
                            'size' => 11,
                            'weight' => 'bold'
                        ],
                        'callback' => 'function(value) {
                            return "R$ " + value.toLocaleString("pt-BR");
                        }'
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Valores em Reais (R$)',
                        'color' => '#00FF00',
                        'font' => [
                            'size' => 12,
                            'weight' => 'bold'
                        ]
                    ]
                ],
                'y' => [
                    'grid' => [
                        'display' => false,
                    ],
                    'ticks' => [
                        'color' => '#FFFFFF',
                        'font' => [
                            'size' => 12,
                            'weight' => 'bold'
                        ]
                    ]
                ]
            ],
            'elements' => [
                'bar' => [
                    'borderWidth' => 2,
                ]
            ],
            'animation' => [
                'duration' => 2000,
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
     * Custom view for professional styling
     */
    protected function getView(): string
    {
        return 'filament.widgets.top-users-infographic';
    }

    /**
     * Check if user can view this widget
     */
    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}