<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class UsersRankingInfographic extends BaseWidget
{
    protected static ?string $heading = '👑 RANKING PROFISSIONAL DE USUÁRIOS';
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = '60s';
    protected static bool $isLazy = true;

    protected function getStats(): array
    {
        // Get top depositors
        $topDepositors = DB::table('deposits')
            ->join('users', 'users.id', '=', 'deposits.user_id')
            ->select('users.name', DB::raw('SUM(deposits.amount) as total'))
            ->where('deposits.status', 1)
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        if ($topDepositors->isEmpty()) {
            // Professional demo data for visualization
            $users = [
                ['name' => 'João Silva', 'amount' => 15000, 'chart' => [3000, 6000, 9000, 12000, 15000], 'icon' => '👑'],
                ['name' => 'Maria Santos', 'amount' => 12500, 'chart' => [2500, 5000, 7500, 10000, 12500], 'icon' => '🥇'],
                ['name' => 'Pedro Costa', 'amount' => 10800, 'chart' => [2160, 4320, 6480, 8640, 10800], 'icon' => '🥈'],
                ['name' => 'Ana Oliveira', 'amount' => 9200, 'chart' => [1840, 3680, 5520, 7360, 9200], 'icon' => '🥉'],
                ['name' => 'Carlos Lima', 'amount' => 7500, 'chart' => [1500, 3000, 4500, 6000, 7500], 'icon' => '⭐'],
            ];
        } else {
            $users = [];
            $icons = ['👑', '🥇', '🥈', '🥉', '⭐'];
            foreach ($topDepositors as $index => $user) {
                $chart = [];
                for ($i = 1; $i <= 5; $i++) {
                    $chart[] = intval($user->total * ($i / 5));
                }
                $users[] = [
                    'name' => substr($user->name, 0, 15) . (strlen($user->name) > 15 ? '...' : ''),
                    'amount' => $user->total,
                    'chart' => $chart,
                    'icon' => $icons[$index] ?? '⭐'
                ];
            }
        }

        $stats = [];
        $colors = ['success', 'warning', 'primary', 'info', 'danger'];
        
        foreach ($users as $index => $user) {
            $stats[] = Stat::make(
                $user['icon'] . ' ' . $user['name'], 
                'R$ ' . number_format($user['amount'], 2, ',', '.')
            )
                ->description('Total Depositado')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart($user['chart'])
                ->color($colors[$index] ?? 'success');
        }

        return $stats;
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}