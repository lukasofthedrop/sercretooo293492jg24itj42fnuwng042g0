<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class UsersRankingColumnChart extends Component
{
    public $chartData = [];
    
    public function mount()
    {
        $this->loadChartData();
    }
    
    public function loadChartData()
    {
        // Consulta real para ranking de usuários por depósitos
        $topUsers = DB::table('deposits')
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
                    '#00ff7f', '#32cd32', '#00ff00', '#90ee90', '#98fb98',
                    '#00fa9a', '#00e676', '#4caf50', '#2e7d32', '#1b5e20'
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