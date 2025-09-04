<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;

class Top5GamesCircularChart extends Component
{
    public $chartData = [];
    
    public function mount()
    {
        $this->loadChartData();
    }
    
    public function loadChartData()
    {
        // Consulta real do banco para top 5 jogos mais jogados
        $topGames = DB::table('orders')
            ->select('game', DB::raw('COUNT(*) as plays'), DB::raw('SUM(amount) as total_amount'))
            ->where('type', 'bet')
            ->whereNotNull('game')
            ->groupBy('game')
            ->orderByDesc('plays')
            ->limit(5)
            ->get();

        if ($topGames->isNotEmpty()) {
            $this->chartData = [
                'labels' => $topGames->pluck('game')->toArray(),
                'data' => $topGames->pluck('plays')->toArray(),
                'amounts' => $topGames->pluck('total_amount')->toArray(),
                'colors' => [
                    '#00ff41', // Verde Matrix principal
                    '#ff6b35', // Laranja vibrante
                    '#4dabf7', // Azul claro
                    '#ffd43b', // Amarelo dourado
                    '#ff8cc8'  // Rosa suave
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