<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class Top5GamesCircularChart extends Component
{
    public $chartData = [];
    
    public function mount()
    {
        $this->loadChartData();
    }
    
    public function loadChartData()
    {
        // Cache do top 5 jogos (15 minutos)
        $topGames = Cache::remember('top5_games_chart_data', 900, function () {
            return DB::table('orders')
                ->select('game', DB::raw('COUNT(*) as plays'), DB::raw('SUM(amount) as total_amount'))
                ->where('type', 'bet')
                ->whereNotNull('game')
                ->groupBy('game')
                ->orderByDesc('plays')
                ->limit(5)
                ->get();
        });

        if ($topGames->isNotEmpty()) {
            $this->chartData = [
                'labels' => $topGames->pluck('game')->toArray(),
                'data' => $topGames->pluck('plays')->toArray(),
                'amounts' => $topGames->pluck('total_amount')->toArray(),
                'colors' => [
                    '#22c55e', // Verde LucrativaBet principal
                    '#4ade80', // Verde claro
                    '#15803d', // Verde escuro  
                    '#06b6d4', // Ciano
                    '#3b82f6'  // Azul profissional
                ]
            ];
        } else {
            // Dados vazios se não houver apostas
            $this->chartData = [
                'labels' => [],
                'data' => [],
                'amounts' => [],
                'colors' => []
            ];
        }
    }

    public function render()
    {
        return view('livewire.top5-games-circular-chart');
    }
}