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
        return [
            'labels' => ['João Silva', 'Maria Santos', 'Pedro Costa', 'Ana Oliveira', 'Carlos Lima'],
            'datasets' => [
                [
                    'label' => 'Total Depositado (R$)',
                    'data' => [15000, 12500, 10800, 9200, 7500],
                    'backgroundColor' => '#00ff00',
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
            'indexAxis' => 'y',
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];
    }

    // /**
    //  * Custom view for professional styling
    //  */
    // protected function getView(): string
    // {
    //     return 'filament.widgets.top-users-infographic';
    // }

    /**
     * Check if user can view this widget
     */
    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}