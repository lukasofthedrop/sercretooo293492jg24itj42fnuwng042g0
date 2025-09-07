<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\User;
use App\Models\Order;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardMetricsController extends Controller
{
    /**
     * Retorna métricas do dashboard para ApexCharts
     */
    public function index(Request $request)
    {
        // TEMPORÁRIO: Desabilitado para testes em desenvolvimento
        // TODO: Reabilitar verificação de admin em produção
        /*
        $user = auth()->user() ?: auth('web')->user();
        
        if (!$user || !$user->hasRole('admin')) {
            if (!session()->has('user_id')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            
            $user = User::find(session('user_id'));
            if (!$user || !$user->hasRole('admin')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }
        */

        $period = $request->get('period', 'month');
        
        $data = [
            'deposits' => $this->getDepositsData($period),
            'users' => $this->getUsersData($period),
            'games' => $this->getGamesData($period),
            'revenue' => $this->getRevenueData($period),
            'stats' => $this->getGeneralStats($period),
            'realtime' => $this->getRealtimeData()
        ];

        return response()->json($data);
    }

    /**
     * Dados de depósitos para gráfico de área
     */
    private function getDepositsData($period = 'month')
    {
        $cacheKey = 'dashboard_deposits_data_' . $period;
        
        return Cache::remember($cacheKey, 60, function () use ($period) {
            $query = Deposit::where('status', 1);
            
            // Aplicar filtro de período
            switch ($period) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    $groupBy = "HOUR(created_at)";
                    $selectRaw = "HOUR(created_at) as hour, SUM(amount) as total";
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', Carbon::yesterday());
                    $groupBy = "HOUR(created_at)";
                    $selectRaw = "HOUR(created_at) as hour, SUM(amount) as total";
                    break;
                case 'week':
                    $query->where('created_at', '>=', Carbon::now()->subDays(7));
                    $groupBy = "DATE(created_at)";
                    $selectRaw = "DATE(created_at) as date, SUM(amount) as total";
                    break;
                default: // month
                    $query->where('created_at', '>=', Carbon::now()->subDays(30));
                    $groupBy = "DATE(created_at)";
                    $selectRaw = "DATE(created_at) as date, SUM(amount) as total";
                    break;
            }
            
            $deposits = $query->selectRaw($selectRaw)
                ->groupBy(DB::raw($groupBy))
                ->orderBy(DB::raw($groupBy), 'ASC')
                ->get();

            if ($period === 'today' || $period === 'yesterday') {
                $baseDate = $period === 'today' ? Carbon::today() : Carbon::yesterday();
                return $deposits->map(function ($item) use ($baseDate) {
                    return [
                        'x' => $baseDate->copy()->hour($item->hour)->timestamp * 1000,
                        'y' => floatval($item->total)
                    ];
                })->toArray();
            }
            
            return $deposits->map(function ($item) {
                return [
                    'x' => Carbon::parse($item->date)->timestamp * 1000,
                    'y' => floatval($item->total)
                ];
            })->toArray();
        });
    }

    /**
     * Dados de usuários para gráfico de linha
     */
    private function getUsersData($period = 'month')
    {
        $cacheKey = 'dashboard_users_data_' . $period;
        
        return Cache::remember($cacheKey, 60, function () use ($period) {
            $query = User::query();
            
            switch ($period) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    $users = $query->selectRaw('HOUR(created_at) as hour, COUNT(*) as total')
                        ->groupBy('hour')
                        ->orderBy('hour', 'ASC')
                        ->get();
                    
                    return [
                        'labels' => $users->pluck('hour')->map(fn($h) => sprintf('%02d:00', $h))->toArray(),
                        'data' => $users->pluck('total')->toArray()
                    ];
                    
                case 'yesterday':
                    $query->whereDate('created_at', Carbon::yesterday());
                    $users = $query->selectRaw('HOUR(created_at) as hour, COUNT(*) as total')
                        ->groupBy('hour')
                        ->orderBy('hour', 'ASC')
                        ->get();
                    
                    return [
                        'labels' => $users->pluck('hour')->map(fn($h) => sprintf('%02d:00', $h))->toArray(),
                        'data' => $users->pluck('total')->toArray()
                    ];
                    
                case 'week':
                    $query->where('created_at', '>=', Carbon::now()->subDays(7));
                    break;
                    
                default: // month
                    $query->where('created_at', '>=', Carbon::now()->subDays(30));
                    break;
            }
            
            $users = $query->selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->groupBy('date')
                ->orderBy('date', 'ASC')
                ->get();

            return [
                'labels' => $users->pluck('date')->map(function ($date) {
                    return Carbon::parse($date)->format('d/m');
                })->toArray(),
                'data' => $users->pluck('total')->toArray()
            ];
        });
    }

    /**
     * Dados de jogos para gráfico donut
     */
    private function getGamesData($period = 'month')
    {
        $cacheKey = 'dashboard_games_data_' . $period;
        
        return Cache::remember($cacheKey, 60, function () use ($period) {
            $query = Order::select('game', DB::raw('COUNT(*) as plays'))
                ->where('type', 'bet')
                ->whereNotNull('game');
            
            switch ($period) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', Carbon::yesterday());
                    break;
                case 'week':
                    $query->where('created_at', '>=', Carbon::now()->subDays(7));
                    break;
                default: // month
                    $query->where('created_at', '>=', Carbon::now()->subDays(30));
                    break;
            }
            
            $topGames = $query->groupBy('game')
                ->orderByDesc('plays')
                ->limit(5)
                ->get();

            if ($topGames->isEmpty()) {
                return [
                    'labels' => [],
                    'data' => []
                ];
            }

            return [
                'labels' => $topGames->pluck('game')->toArray(),
                'data' => $topGames->pluck('plays')->toArray()
            ];
        });
    }

    /**
     * Dados de receita para gráfico de barras
     */
    private function getRevenueData($period = 'month')
    {
        $cacheKey = 'dashboard_revenue_data_' . $period;
        
        return Cache::remember($cacheKey, 60, function () use ($period) {
            $data = [];
            
            switch ($period) {
                case 'today':
                    // Para hoje, mostrar por hora
                    for ($i = 0; $i < 24; $i++) {
                        $startHour = Carbon::today()->hour($i);
                        $endHour = $startHour->copy()->addHour();
                        
                        $deposits = Deposit::whereBetween('created_at', [$startHour, $endHour])
                            ->where('status', 1)
                            ->sum('amount');
                        
                        $withdrawals = Withdrawal::whereBetween('created_at', [$startHour, $endHour])
                            ->where('status', 1)
                            ->sum('amount');
                        
                        $data['labels'][] = sprintf('%02d:00', $i);
                        $data['receita'][] = floatval($deposits);
                        $data['lucro'][] = floatval($deposits - $withdrawals);
                    }
                    break;
                    
                case 'yesterday':
                    // Para ontem, mostrar por hora
                    for ($i = 0; $i < 24; $i++) {
                        $startHour = Carbon::yesterday()->hour($i);
                        $endHour = $startHour->copy()->addHour();
                        
                        $deposits = Deposit::whereBetween('created_at', [$startHour, $endHour])
                            ->where('status', 1)
                            ->sum('amount');
                        
                        $withdrawals = Withdrawal::whereBetween('created_at', [$startHour, $endHour])
                            ->where('status', 1)
                            ->sum('amount');
                        
                        $data['labels'][] = sprintf('%02d:00', $i);
                        $data['receita'][] = floatval($deposits);
                        $data['lucro'][] = floatval($deposits - $withdrawals);
                    }
                    break;
                    
                case 'week':
                    $days = 7;
                    break;
                    
                default: // month
                    $days = 30;
                    break;
            }
            
            // Para semana e mês, mostrar por dia
            if (in_array($period, ['week', 'month'])) {
                for ($i = $days - 1; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    
                    $deposits = Deposit::whereDate('created_at', $date)
                        ->where('status', 1)
                        ->sum('amount');
                    
                    $withdrawals = Withdrawal::whereDate('created_at', $date)
                        ->where('status', 1)
                        ->sum('amount');
                    
                    $data['labels'][] = $date->format('d/m');
                    $data['receita'][] = floatval($deposits);
                    $data['lucro'][] = floatval($deposits - $withdrawals);
                }
            }
            
            return $data;
        });
    }

    /**
     * Estatísticas gerais
     */
    private function getGeneralStats($period = 'month')
    {
        $cacheKey = 'dashboard_general_stats_' . $period;
        
        return Cache::remember($cacheKey, 60, function () use ($period) {
            // Definir período de análise
            switch ($period) {
                case 'today':
                    $startDate = Carbon::today();
                    $endDate = Carbon::now();
                    $compareStart = Carbon::yesterday();
                    $compareEnd = Carbon::yesterday()->endOfDay();
                    break;
                case 'yesterday':
                    $startDate = Carbon::yesterday();
                    $endDate = Carbon::yesterday()->endOfDay();
                    $compareStart = Carbon::yesterday()->subDay();
                    $compareEnd = Carbon::yesterday()->subDay()->endOfDay();
                    break;
                case 'week':
                    $startDate = Carbon::now()->subDays(7);
                    $endDate = Carbon::now();
                    $compareStart = Carbon::now()->subDays(14);
                    $compareEnd = Carbon::now()->subDays(7);
                    break;
                default: // month
                    $startDate = Carbon::now()->subDays(30);
                    $endDate = Carbon::now();
                    $compareStart = Carbon::now()->subDays(60);
                    $compareEnd = Carbon::now()->subDays(30);
                    break;
            }
            
            // Total de depósitos no período
            $deposits = Deposit::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 1)
                ->sum('amount');
            
            // Total de depósitos no período anterior (para comparação)
            $depositsCompare = Deposit::whereBetween('created_at', [$compareStart, $compareEnd])
                ->where('status', 1)
                ->sum('amount');
            
            // Total de saques no período
            $withdrawals = Withdrawal::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 1)
                ->sum('amount');
            
            // Novos usuários no período
            $newUsers = User::whereBetween('created_at', [$startDate, $endDate])->count();
            $newUsersCompare = User::whereBetween('created_at', [$compareStart, $compareEnd])->count();
            
            // Total de apostas no período
            $bets = Order::where('type', 'bet')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
            $betsCompare = Order::where('type', 'bet')
                ->whereBetween('created_at', [$compareStart, $compareEnd])
                ->count();
            
            // Calcular mudanças percentuais
            $depositsChange = $depositsCompare > 0 
                ? round((($deposits - $depositsCompare) / $depositsCompare) * 100, 1) 
                : 0;
            $usersChange = $newUsersCompare > 0 
                ? round((($newUsers - $newUsersCompare) / $newUsersCompare) * 100, 1) 
                : 0;
            $betsChange = $betsCompare > 0 
                ? round((($bets - $betsCompare) / $betsCompare) * 100, 1) 
                : 0;
            
            $profit = $deposits - $withdrawals;
            $profitCompare = $depositsCompare - Withdrawal::whereBetween('created_at', [$compareStart, $compareEnd])
                ->where('status', 1)
                ->sum('amount');
            $profitChange = $profitCompare != 0 
                ? round((($profit - $profitCompare) / abs($profitCompare)) * 100, 1) 
                : 0;
            
            return [
                'total_deposits' => $deposits,
                'total_deposits_change' => $depositsChange,
                'total_users' => $newUsers,
                'total_users_change' => $usersChange,
                'total_bets' => $bets,
                'total_bets_change' => $betsChange,
                'total_profit' => $profit,
                'total_profit_change' => $profitChange,
                'withdrawals' => $withdrawals,
            ];
        });
    }

    /**
     * Dados em tempo real (sem cache)
     */
    private function getRealtimeData()
    {
        $lastMinute = Carbon::now()->subMinute();
        
        return [
            'active_users' => User::where('updated_at', '>=', $lastMinute)->count(),
            'recent_deposits' => Deposit::where('created_at', '>=', $lastMinute)
                ->where('status', 1)
                ->count(),
            'recent_bets' => Order::where('type', 'bet')
                ->where('created_at', '>=', $lastMinute)
                ->count(),
            'timestamp' => now()->timestamp
        ];
    }

    /**
     * Endpoint para sparklines individuais
     */
    public function sparkline(Request $request, $type)
    {
        // TEMPORÁRIO: Desabilitado para testes
        /*if (!auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }*/

        $data = [];
        $hours = 24;

        switch ($type) {
            case 'deposits':
                for ($i = $hours - 1; $i >= 0; $i--) {
                    $hour = Carbon::now()->subHours($i);
                    $value = Deposit::where('status', 1)
                        ->whereBetween('created_at', [$hour, $hour->copy()->addHour()])
                        ->sum('amount');
                    $data[] = floatval($value);
                }
                break;
                
            case 'users':
                for ($i = $hours - 1; $i >= 0; $i--) {
                    $hour = Carbon::now()->subHours($i);
                    $value = User::whereBetween('created_at', [$hour, $hour->copy()->addHour()])
                        ->count();
                    $data[] = $value;
                }
                break;
                
            case 'bets':
                for ($i = $hours - 1; $i >= 0; $i--) {
                    $hour = Carbon::now()->subHours($i);
                    $value = Order::where('type', 'bet')
                        ->whereBetween('created_at', [$hour, $hour->copy()->addHour()])
                        ->count();
                    $data[] = $value;
                }
                break;
        }

        return response()->json(['data' => $data]);
    }

    /**
     * Exportar dados do dashboard
     */
    public function export(Request $request)
    {
        // TEMPORÁRIO: Desabilitado para testes
        /*if (!auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }*/

        $format = $request->get('format', 'json');
        $period = $request->get('period', 'month');

        $startDate = match ($period) {
            'week' => Carbon::now()->subWeek(),
            'month' => Carbon::now()->subMonth(),
            'year' => Carbon::now()->subYear(),
            default => Carbon::now()->subMonth()
        };

        $data = [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => now()->format('Y-m-d')
            ],
            'deposits' => Deposit::where('status', 1)
                ->where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total')
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->get(),
            'withdrawals' => Withdrawal::where('status', 1)
                ->where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total')
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->get(),
            'users' => User::where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
                ->groupBy('date')
                ->orderBy('date', 'DESC')
                ->get(),
            'games' => Order::where('type', 'bet')
                ->where('created_at', '>=', $startDate)
                ->selectRaw('game, COUNT(*) as plays, SUM(amount) as total')
                ->groupBy('game')
                ->orderByDesc('plays')
                ->limit(20)
                ->get()
        ];

        if ($format === 'csv') {
            // Implementar exportação CSV
            return $this->exportAsCSV($data);
        }

        return response()->json($data);
    }

    /**
     * Exportar como CSV
     */
    private function exportAsCSV($data)
    {
        $csv = "Dashboard Report - " . now()->format('Y-m-d H:i:s') . "\n\n";
        
        // Depósitos
        $csv .= "DEPOSITS\n";
        $csv .= "Date,Count,Total\n";
        foreach ($data['deposits'] as $deposit) {
            $csv .= "{$deposit->date},{$deposit->count},{$deposit->total}\n";
        }
        
        $csv .= "\nWITHDRAWALS\n";
        $csv .= "Date,Count,Total\n";
        foreach ($data['withdrawals'] as $withdrawal) {
            $csv .= "{$withdrawal->date},{$withdrawal->count},{$withdrawal->total}\n";
        }
        
        $csv .= "\nNEW USERS\n";
        $csv .= "Date,Total\n";
        foreach ($data['users'] as $user) {
            $csv .= "{$user->date},{$user->total}\n";
        }
        
        $csv .= "\nTOP GAMES\n";
        $csv .= "Game,Plays,Total\n";
        foreach ($data['games'] as $game) {
            $csv .= "\"{$game->game}\",{$game->plays},{$game->total}\n";
        }
        
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="dashboard-report-' . now()->format('Y-m-d') . '.csv"'
        ]);
    }

    /**
     * Gerar dados de teste para visualização
     * APENAS PARA DESENVOLVIMENTO - REMOVER EM PRODUÇÃO
     */
    public function generateTestData(Request $request)
    {
        // NOVO: Gerar dados REAIS no banco de dados
        $this->generateRealTestData();
        
        $period = $request->get('period', 'today');
        
        // Gerar dados de depósitos
        $deposits = [];
        if ($period === 'today') {
            $now = now();
            for ($i = 0; $i < 24; $i++) {
                $hour = $now->copy()->hour($i);
                $value = 0;
                
                // Simular picos em horários específicos
                if ($i >= 8 && $i <= 23) {
                    $value = rand(500, 5000);
                    if ($i >= 19 && $i <= 22) {
                        $value = rand(3000, 8000); // Pico noturno
                    }
                }
                
                $deposits[] = [
                    'x' => $hour->timestamp * 1000,
                    'y' => $value
                ];
            }
        }
        
        // Gerar dados de usuários
        $usersLabels = [];
        $usersData = [];
        for ($i = 0; $i < 24; $i++) {
            $usersLabels[] = sprintf('%02d:00', $i);
            $userCount = 0;
            
            if ($i >= 6 && $i <= 23) {
                $userCount = rand(2, 20);
                if ($i >= 20 && $i <= 22) {
                    $userCount = rand(15, 35); // Pico noturno
                }
            }
            
            $usersData[] = $userCount;
        }
        
        // Gerar dados de jogos
        $games = [
            'Gates of Olympus',
            'Fortune Tiger', 
            'Sweet Bonanza',
            'Aviator',
            'Spaceman',
            'Mines',
            'Fortune Ox',
            'Sugar Rush'
        ];
        
        $gamesData = [];
        $gamesLabels = [];
        
        // Selecionar 5 jogos aleatórios
        shuffle($games);
        for ($i = 0; $i < min(5, count($games)); $i++) {
            $gamesLabels[] = $games[$i];
            $gamesData[] = rand(10, 100);
        }
        
        // Gerar dados de receita
        $revenueLabels = [];
        $receita = [];
        $lucro = [];
        
        if ($period === 'today') {
            for ($i = 0; $i < 24; $i++) {
                $revenueLabels[] = sprintf('%02d:00', $i);
                $depositValue = 0;
                $profitValue = 0;
                
                if ($i >= 8 && $i <= 23) {
                    $depositValue = rand(1000, 10000);
                    $profitValue = $depositValue * 0.15; // 15% de lucro médio
                }
                
                $receita[] = $depositValue;
                $lucro[] = $profitValue;
            }
        }
        
        // Gerar estatísticas gerais
        $stats = [
            'total_deposits' => rand(50000, 150000),
            'total_deposits_change' => rand(-20, 50),
            'total_users' => rand(50, 200),
            'total_users_change' => rand(-10, 30),
            'total_bets' => rand(500, 2000),
            'total_bets_change' => rand(-15, 40),
            'total_profit' => rand(10000, 30000),
            'total_profit_change' => rand(-25, 60),
            'withdrawals' => rand(5000, 20000),
        ];
        
        // Dados em tempo real
        $realtime = [
            'active_users' => rand(10, 50),
            'recent_deposits' => rand(1, 10),
            'recent_bets' => rand(5, 30),
            'timestamp' => now()->timestamp
        ];
        
        $data = [
            'test_mode' => true,
            'message' => 'DADOS DE TESTE - Para visualização apenas',
            'deposits' => $deposits,
            'users' => [
                'labels' => $usersLabels,
                'data' => $usersData
            ],
            'games' => [
                'labels' => $gamesLabels,
                'data' => $gamesData
            ],
            'revenue' => [
                'labels' => $revenueLabels,
                'receita' => $receita,
                'lucro' => $lucro
            ],
            'stats' => $stats,
            'realtime' => $realtime
        ];
        
        // SALVAR DADOS DE TESTE NO CACHE PARA OS WIDGETS FUNCIONAREM
        
        // 1. Salvar dados do Top 5 Games no formato esperado pelo widget
        $top5GamesTestData = collect();
        $gamesAmounts = [2097, 1161, 1322, 1942, 954]; // Valores de receita
        for ($i = 0; $i < count($gamesLabels); $i++) {
            $top5GamesTestData->push((object)[
                'game' => $gamesLabels[$i],
                'plays' => $gamesData[$i],
                'total_amount' => $gamesAmounts[$i] ?? rand(500, 2500)
            ]);
        }
        Cache::put('top5_games_chart_data', $top5GamesTestData, 900); // 15 minutos
        
        // 2. Salvar dados do Users Ranking no formato esperado pelo widget  
        $usersTestNames = [
            'Teste CPF Auto', 'Admin LucrativaBet', 'Teste Demo', 
            'Teste AureoLink', 'Admin', 'Teste Usuario 2',
            'Teste VIP 1', 'Teste VIP 2', 'Demo User'
        ];
        $usersTestEmails = [
            'teste1@email.com', 'admin@lucrativa.com', 'demo@test.com',
            'aureo@test.com', 'admin@test.com', 'usuario2@test.com', 
            'vip1@test.com', 'vip2@test.com', 'demo@user.com'
        ];
        $usersTestDeposits = [4, 3, 2, 3, 3, 3, 2, 2, 1];
        $usersTestAmounts = [2149, 1708, 1454, 769, 712, 600, 450, 350, 200];
        
        $usersRankingTestData = collect();
        for ($i = 0; $i < min(count($usersTestNames), 9); $i++) {
            $usersRankingTestData->push((object)[
                'name' => $usersTestNames[$i],
                'email' => $usersTestEmails[$i],
                'total_deposited' => $usersTestAmounts[$i],
                'total_deposits' => $usersTestDeposits[$i]
            ]);
        }
        Cache::put('users_ranking_chart_data', $usersRankingTestData, 1800); // 30 minutos
        
        return response()->json($data);
    }

    /**
     * Gerar dados REAIS de teste no banco de dados
     */
    private function generateRealTestData()
    {
        try {
            DB::beginTransaction();
            
            // 1. Criar usuários de teste (se não existirem)
            $testUsers = [];
            $userEmails = [
                'teste1@lucrativabet.com',
                'teste2@lucrativabet.com', 
                'teste3@lucrativabet.com',
                'teste4@lucrativabet.com',
                'teste5@lucrativabet.com'
            ];
            
            foreach ($userEmails as $index => $email) {
                $user = User::where('email', $email)->first();
                if (!$user) {
                    $user = User::create([
                        'name' => 'Teste User ' . ($index + 1),
                        'email' => $email,
                        'password' => bcrypt('teste123'),
                        'email_verified_at' => now(),
                        'created_at' => now()->subHours(rand(1, 24))
                    ]);
                }
                $testUsers[] = $user;
                
                // Criar carteira se não existir
                $wallet = DB::table('wallets')->where('user_id', $user->id)->first();
                if (!$wallet) {
                    DB::table('wallets')->insert([
                        'user_id' => $user->id,
                        'balance' => rand(100, 5000),
                        'balance_bonus' => rand(0, 500),
                        'balance_withdrawal' => 0,
                        'refer_rewards' => 0,
                        'total_bet' => rand(100, 3000),
                        'total_won' => rand(50, 2000),
                        'total_lose' => rand(50, 1000),
                        'last_won' => rand(10, 500),
                        'last_lose' => rand(10, 200),
                        'currency' => 'BRL',
                        'vip_level' => rand(0, 3),
                        'vip_points' => rand(0, 1000),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            
            // 2. Criar depósitos de teste para hoje
            $now = now();
            foreach ($testUsers as $user) {
                // Criar 2-4 depósitos por usuário
                $numDeposits = rand(2, 4);
                for ($i = 0; $i < $numDeposits; $i++) {
                    DB::table('deposits')->insert([
                        'user_id' => $user->id,
                        'amount' => rand(50, 1000),
                        'type' => 'PIX',
                        'status' => 1, // Aprovado
                        'currency' => 'BRL',
                        'symbol' => 'R$',
                        'created_at' => $now->copy()->subHours(rand(0, 23)),
                        'updated_at' => now(),
                    ]);
                }
            }
            
            // 3. Criar alguns saques de teste
            foreach ($testUsers as $user) {
                if (rand(0, 1)) { // 50% de chance de ter saque
                    DB::table('withdrawals')->insert([
                        'user_id' => $user->id,
                        'amount' => rand(100, 500),
                        'type' => 'PIX',
                        'status' => rand(0, 1), // 50% aprovado, 50% pendente
                        'pix_key' => 'teste@pix.com',
                        'pix_type' => 'email',
                        'currency' => 'BRL',
                        'symbol' => 'R$',
                        'created_at' => $now->copy()->subHours(rand(0, 12)),
                        'updated_at' => now(),
                    ]);
                }
            }
            
            // 4. Criar apostas (orders) de teste
            $games = [
                'Gates of Olympus', 'Fortune Tiger', 'Sweet Bonanza',
                'Aviator', 'Spaceman', 'Mines', 'Fortune Ox', 'Sugar Rush'
            ];
            
            foreach ($testUsers as $user) {
                // Criar 5-10 apostas por usuário
                $numBets = rand(5, 10);
                for ($i = 0; $i < $numBets; $i++) {
                    $amount = rand(10, 200);
                    $won = rand(0, 1); // 50% de chance de ganhar
                    
                    DB::table('orders')->insert([
                        'user_id' => $user->id,
                        'session_id' => uniqid(),
                        'transaction_id' => uniqid(),
                        'type' => 'bet',
                        'type_money' => 'balance',
                        'amount' => $amount,
                        'providers' => 'PragmaticPlay',
                        'game' => $games[array_rand($games)],
                        'game_uuid' => uniqid(),
                        'profit' => $won ? rand($amount * 0.5, $amount * 3) : -$amount,
                        'status' => 1,
                        'created_at' => $now->copy()->subHours(rand(0, 23)),
                        'updated_at' => now(),
                    ]);
                }
            }
            
            // 5. Criar transações de teste
            foreach ($testUsers as $user) {
                // Transações de depósito
                DB::table('transactions')->insert([
                    'user_id' => $user->id,
                    'payment_id' => uniqid(),
                    'type' => 'deposit',
                    'type_money' => 'balance',
                    'amount' => rand(100, 1000),
                    'providers' => 'PIX',
                    'game' => null,
                    'game_uuid' => null,
                    'created_at' => $now->copy()->subHours(rand(0, 23)),
                    'updated_at' => now(),
                ]);
            }
            
            DB::commit();
            
            // Limpar TODOS os caches específicos do dashboard
            $today = now()->format('Y-m-d');
            
            // Limpar caches do StatsOverview
            Cache::forget("stats_financial_{$today}");
            Cache::forget('stats_player_balance');
            Cache::forget('stats_affiliate_rewards');
            Cache::forget('stats_deposit_counts');
            Cache::forget('stats_total_users');
            
            // Limpar caches gerais do dashboard
            Cache::forget('dashboard_metrics_today');
            Cache::forget('dashboard_metrics_yesterday');
            Cache::forget('dashboard_metrics_week');
            Cache::forget('dashboard_metrics_month');
            Cache::forget('dashboard_metrics_sparkline_deposits');
            Cache::forget('dashboard_metrics_sparkline_users');
            
            // Limpar cache geral como backup
            Cache::flush();
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Erro ao gerar dados de teste: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Limpar cache do dashboard (reset)
     */
    public function clearCache()
    {
        // Limpar todos os caches do dashboard
        $periods = ['today', 'yesterday', 'week', 'month'];
        $types = ['deposits', 'users', 'games', 'revenue', 'general_stats'];
        
        foreach ($periods as $period) {
            foreach ($types as $type) {
                Cache::forget("dashboard_{$type}_data_{$period}");
            }
        }
        
        // Limpar cache adicional
        Cache::forget('users_ranking_chart_data');
        Cache::forget('top5_games_chart_data');
        
        return response()->json([
            'success' => true,
            'message' => 'Cache do dashboard limpo com sucesso!'
        ]);
    }

    /**
     * Reset completo do sistema para operação real
     * Remove todos os dados de teste mantendo apenas admins
     */
    public function resetSystem(Request $request)
    {
        // Verificar se é admin
        $user = auth()->user() ?: auth('web')->user();
        
        // Verificar se é admin (por role ou email)
        $isAdmin = false;
        if ($user) {
            // Verificar por role se existir o método
            if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
                $isAdmin = true;
            }
            // Ou verificar pelo email - INCLUÍDO lucrativa@bet.com
            elseif (in_array($user->email, ['lucrativa@bet.com', 'admin@admin.com', 'admin@lucrativabet.com', 'dev@lucrativabet.com'])) {
                $isAdmin = true;
            }
        }
        
        if (!$isAdmin) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        // Confirmar senha do admin para segurança extra
        if (!$request->has('confirm_password')) {
            return response()->json([
                'error' => 'Senha de confirmação necessária',
                'require_password' => true
            ], 400);
        }

        if (!password_verify($request->confirm_password, $user->password)) {
            return response()->json(['error' => 'Senha incorreta'], 401);
        }

        try {
            // 1. Criar backup antes do reset
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $backupDir = storage_path('backups/reset_' . $timestamp);
            
            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0777, true);
            }

            // Backup das tabelas principais
            $tables = ['users', 'deposits', 'orders', 'withdrawals', 'wallets', 'transactions'];
            foreach ($tables as $table) {
                try {
                    $data = DB::table($table)->get();
                    file_put_contents($backupDir . '/' . $table . '.json', json_encode($data, JSON_PRETTY_PRINT));
                } catch (\Exception $e) {
                    // Log mas não para o processo por backup
                    \Log::warning("Erro no backup da tabela {$table}: " . $e->getMessage());
                }
            }

            // 2. IDs dos admins a preservar - INCLUÍDO lucrativa@bet.com COMO PRINCIPAL
            $adminEmails = ['lucrativa@bet.com', 'admin@admin.com', 'admin@lucrativabet.com', 'dev@lucrativabet.com'];
            $adminIds = User::whereIn('email', $adminEmails)->pluck('id')->toArray();

            // 3. Executar operações de reset em blocos separados para evitar problemas de transação
            
            // Desabilitar foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // 4. Limpar tabelas de transações individualmente
            try { DB::table('deposits')->truncate(); } catch (\Exception $e) { \Log::warning('Erro ao truncar deposits: ' . $e->getMessage()); }
            try { DB::table('orders')->truncate(); } catch (\Exception $e) { \Log::warning('Erro ao truncar orders: ' . $e->getMessage()); }
            try { DB::table('withdrawals')->truncate(); } catch (\Exception $e) { \Log::warning('Erro ao truncar withdrawals: ' . $e->getMessage()); }
            try { DB::table('transactions')->truncate(); } catch (\Exception $e) { \Log::warning('Erro ao truncar transactions: ' . $e->getMessage()); }
            try { DB::table('affiliate_histories')->truncate(); } catch (\Exception $e) { \Log::warning('Erro ao truncar affiliate_histories: ' . $e->getMessage()); }

            // 5. Resetar carteiras (manter apenas dos admins com saldo zero)
            try { 
                DB::table('wallets')->whereNotIn('user_id', $adminIds)->delete();
                DB::table('wallets')->whereIn('user_id', $adminIds)->update([
                    'balance' => 0,
                    'balance_bonus' => 0,
                    'balance_withdrawal' => 0,
                    'total_bet' => 0,
                    'total_won' => 0,
                    'total_lose' => 0,
                    'last_won' => 0,
                    'last_lose' => 0,
                ]);
            } catch (\Exception $e) { 
                \Log::warning('Erro ao resetar wallets: ' . $e->getMessage()); 
            }

            // 6. Remover usuários de teste (manter apenas admins)
            try {
                User::whereNotIn('id', $adminIds)->delete();
            } catch (\Exception $e) {
                \Log::warning('Erro ao deletar usuários: ' . $e->getMessage());
            }

            // 7. Reabilitar foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            // 8. Limpar todo o cache
            try {
                Cache::flush();
                \Artisan::call('cache:clear');
                \Artisan::call('config:clear');
                \Artisan::call('view:clear');
            } catch (\Exception $e) {
                \Log::warning('Erro ao limpar cache: ' . $e->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Sistema resetado com sucesso! Pronto para operação real.',
                'backup_path' => $backupDir,
                'stats' => [
                    'users_remaining' => count($adminIds),
                    'deposits' => 0,
                    'orders' => 0,
                    'total_balance' => 0
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Erro ao resetar sistema',
                'message' => $e->getMessage(),
                'details' => 'Verifique os logs para mais detalhes'
            ], 500);
        }
    }
}