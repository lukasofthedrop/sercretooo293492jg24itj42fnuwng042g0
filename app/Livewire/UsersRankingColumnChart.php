<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class UsersRankingColumnChart extends Component
{
    public $chartData = [];
    
    public function mount()
    {
        $this->loadChartData();
    }
    
    public function loadChartData()
    {
        // Cache do ranking de usuários (30 minutos)
        $topUsers = Cache::remember('users_ranking_chart_data', 1800, function () {
            return DB::table('deposits')
                ->join('users', 'users.id', '=', 'deposits.user_id')
                ->select(
                    'users.name', 
                    'users.email',
                    DB::raw('SUM(deposits.amount) as total_deposited'),
                    DB::raw('COUNT(deposits.id) as total_deposits')
                )
                ->where('deposits.status', 1)
                ->groupBy('users.id', 'users.name', 'users.email')
                ->orderByDesc('total_deposited')
                ->limit(10)
                ->get();
        });

        if ($topUsers->isNotEmpty()) {
            $names = [];
            $deposits = [];
            $amounts = [];
            $emails = [];

            foreach ($topUsers as $user) {
                // Limitar nome para exibição
                $displayName = strlen($user->name) > 12 ? substr($user->name, 0, 12) . '...' : $user->name;
                $names[] = $displayName;
                $deposits[] = $user->total_deposits;
                $amounts[] = floatval($user->total_deposited);
                $emails[] = $user->email;
            }

            $this->chartData = [
                'labels' => $names,
                'fullNames' => $topUsers->pluck('name')->toArray(),
                'emails' => $emails,
                'deposits' => $deposits,
                'amounts' => $amounts,
                'colors' => [
                    '#22c55e', // Verde LucrativaBet principal
                    '#4ade80', // Verde claro
                    '#15803d', // Verde escuro
                    '#06b6d4', // Ciano profissional
                    '#3b82f6', // Azul profissional  
                    '#10b981', // Verde esmeralda
                    '#14b8a6', // Teal
                    '#059669', // Verde médio
                    '#16a34a', // Verde padrão
                    '#166534'  // Verde muito escuro
                ]
            ];
        } else {
            $this->chartData = [
                'labels' => [],
                'fullNames' => [],
                'emails' => [],
                'deposits' => [],
                'amounts' => [],
                'colors' => []
            ];
        }
    }

    public function render()
    {
        return view('livewire.users-ranking-column-chart');
    }
}