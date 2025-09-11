<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AffiliateStatistics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = 'AFILIADO';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationLabel = 'Estatísticas Avançadas';
    protected static ?string $title = 'Análise Detalhada de Performance';
    protected static string $view = 'filament.pages.affiliate-statistics';
    
    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && $user->inviter_code && $user->hasRole('afiliado');
    }
    
    public function getViewData(): array
    {
        $selectedPeriod = request()->get('period', '30');
        $statistics = $this->loadStatistics($selectedPeriod);
        
        return [
            'selectedPeriod' => $selectedPeriod,
            'statistics' => $statistics,
        ];
    }
    
    public function loadStatistics($selectedPeriod = '30'): array
    {
        $userId = auth()->id();
        $referredUsers = User::where('inviter', $userId)->pluck('id');
        
        // Período selecionado
        $startDate = Carbon::now()->subDays($selectedPeriod);
        $endDate = Carbon::now();
        
        $statistics = [];
        
        // Estatísticas gerais
        $statistics['overview'] = $this->getOverviewStats($referredUsers, $startDate, $endDate);
        
        // Performance por período
        $statistics['performance'] = $this->getPerformanceByPeriod($referredUsers);
        
        // Top usuários
        $statistics['topUsers'] = $this->getTopUsers($referredUsers);
        
        // Análise por dia da semana
        $statistics['weekdayAnalysis'] = $this->getWeekdayAnalysis($referredUsers, $startDate, $endDate);
        
        // Análise por hora
        $statistics['hourlyAnalysis'] = $this->getHourlyAnalysis($referredUsers, $startDate, $endDate);
        
        // Taxa de retenção
        $statistics['retention'] = $this->getRetentionAnalysis($referredUsers);
        
        // Previsão de ganhos
        $statistics['forecast'] = $this->getForecast($referredUsers);
        
        return $statistics;
    }
    
    protected function getOverviewStats($userIds, $startDate, $endDate)
    {
        $totalDeposits = Deposit::whereIn('user_id', $userIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 1)
            ->sum('amount');
            
        $totalWithdrawals = Withdrawal::whereIn('user_id', $userIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 1)
            ->sum('amount');
            
        $ngr = $totalDeposits - $totalWithdrawals;
        
        $totalUsers = count($userIds);
        $activeUsers = User::whereIn('id', $userIds)
            ->whereHas('deposits', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('status', 1);
            })
            ->count();
            
        $newUsers = User::whereIn('id', $userIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
            
        // Calcula médias
        $avgDepositValue = $totalUsers > 0 ? $totalDeposits / $totalUsers : 0;
        $avgUserValue = $activeUsers > 0 ? $ngr / $activeUsers : 0;
        
        return [
            'total_deposits' => $totalDeposits,
            'total_withdrawals' => $totalWithdrawals,
            'ngr' => $ngr,
            'commission' => $ngr * 0.40, // Mostra 40%
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'new_users' => $newUsers,
            'conversion_rate' => $totalUsers > 0 ? ($activeUsers / $totalUsers) * 100 : 0,
            'avg_deposit_value' => $avgDepositValue,
            'avg_user_value' => $avgUserValue,
        ];
    }
    
    protected function getPerformanceByPeriod($userIds)
    {
        $data = [];
        
        // Últimos 12 meses
        for ($i = 11; $i >= 0; $i--) {
            $startDate = Carbon::now()->subMonths($i)->startOfMonth();
            $endDate = Carbon::now()->subMonths($i)->endOfMonth();
            
            $deposits = Deposit::whereIn('user_id', $userIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 1)
                ->sum('amount');
                
            $withdrawals = Withdrawal::whereIn('user_id', $userIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 1)
                ->sum('amount');
                
            $ngr = $deposits - $withdrawals;
            
            $newUsers = User::whereIn('id', $userIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
                
            $data[] = [
                'month' => $startDate->format('M/Y'),
                'deposits' => $deposits,
                'withdrawals' => $withdrawals,
                'ngr' => $ngr,
                'commission' => $ngr * 0.40, // Mostra 40%
                'new_users' => $newUsers,
            ];
        }
        
        return $data;
    }
    
    protected function getTopUsers($userIds)
    {
        return User::whereIn('id', $userIds)
            ->with(['deposits' => function($q) {
                $q->where('status', 1);
            }])
            ->get()
            ->map(function($user) {
                $totalDeposits = $user->deposits->sum('amount');
                $totalWithdrawals = Withdrawal::where('user_id', $user->id)
                    ->where('status', 1)
                    ->sum('amount');
                $ngr = $totalDeposits - $totalWithdrawals;
                
                return [
                    'name' => $user->name,
                    'email' => substr($user->email, 0, 3) . '***' . substr($user->email, strpos($user->email, '@')),
                    'total_deposits' => $totalDeposits,
                    'ngr' => $ngr,
                    'commission' => $ngr * 0.40, // Mostra 40%
                    'created_at' => $user->created_at->format('d/m/Y'),
                ];
            })
            ->sortByDesc('ngr')
            ->take(10)
            ->values();
    }
    
    protected function getWeekdayAnalysis($userIds, $startDate, $endDate)
    {
        $weekdays = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'];
        $data = [];
        
        for ($i = 0; $i < 7; $i++) {
            $deposits = Deposit::whereIn('user_id', $userIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 1)
                ->whereRaw('DAYOFWEEK(created_at) = ?', [$i + 1])
                ->sum('amount');
                
            $count = Deposit::whereIn('user_id', $userIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 1)
                ->whereRaw('DAYOFWEEK(created_at) = ?', [$i + 1])
                ->count();
                
            $data[] = [
                'day' => $weekdays[$i],
                'total' => $deposits,
                'count' => $count,
                'average' => $count > 0 ? $deposits / $count : 0,
            ];
        }
        
        return $data;
    }
    
    protected function getHourlyAnalysis($userIds, $startDate, $endDate)
    {
        $data = [];
        
        for ($hour = 0; $hour < 24; $hour++) {
            $deposits = Deposit::whereIn('user_id', $userIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 1)
                ->whereRaw('HOUR(created_at) = ?', [$hour])
                ->sum('amount');
                
            $count = Deposit::whereIn('user_id', $userIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 1)
                ->whereRaw('HOUR(created_at) = ?', [$hour])
                ->count();
                
            $data[] = [
                'hour' => sprintf('%02d:00', $hour),
                'total' => $deposits,
                'count' => $count,
            ];
        }
        
        return $data;
    }
    
    protected function getRetentionAnalysis($userIds)
    {
        $cohorts = [];
        
        // Análise de retenção por coorte mensal
        for ($i = 5; $i >= 0; $i--) {
            $cohortStart = Carbon::now()->subMonths($i)->startOfMonth();
            $cohortEnd = Carbon::now()->subMonths($i)->endOfMonth();
            
            $cohortUsers = User::whereIn('id', $userIds)
                ->whereBetween('created_at', [$cohortStart, $cohortEnd])
                ->pluck('id');
                
            $totalCohort = count($cohortUsers);
            
            if ($totalCohort > 0) {
                $retention = [];
                
                for ($j = 0; $j <= $i; $j++) {
                    $checkStart = $cohortStart->copy()->addMonths($j)->startOfMonth();
                    $checkEnd = $cohortStart->copy()->addMonths($j)->endOfMonth();
                    
                    $activeInMonth = User::whereIn('id', $cohortUsers)
                        ->whereHas('deposits', function($q) use ($checkStart, $checkEnd) {
                            $q->whereBetween('created_at', [$checkStart, $checkEnd])
                              ->where('status', 1);
                        })
                        ->count();
                        
                    $retention[] = [
                        'month' => $j,
                        'percentage' => ($activeInMonth / $totalCohort) * 100,
                        'count' => $activeInMonth,
                    ];
                }
                
                $cohorts[] = [
                    'cohort' => $cohortStart->format('M/Y'),
                    'total' => $totalCohort,
                    'retention' => $retention,
                ];
            }
        }
        
        return $cohorts;
    }
    
    protected function getForecast($userIds)
    {
        // Análise dos últimos 3 meses para projeção
        $historicalData = [];
        
        for ($i = 2; $i >= 0; $i--) {
            $startDate = Carbon::now()->subMonths($i)->startOfMonth();
            $endDate = Carbon::now()->subMonths($i)->endOfMonth();
            
            $deposits = Deposit::whereIn('user_id', $userIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 1)
                ->sum('amount');
                
            $withdrawals = Withdrawal::whereIn('user_id', $userIds)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 1)
                ->sum('amount');
                
            $historicalData[] = $deposits - $withdrawals;
        }
        
        // Calcula tendência simples
        $avgGrowth = 0;
        if (count($historicalData) > 1) {
            for ($i = 1; $i < count($historicalData); $i++) {
                if ($historicalData[$i - 1] > 0) {
                    $avgGrowth += (($historicalData[$i] - $historicalData[$i - 1]) / $historicalData[$i - 1]);
                }
            }
            $avgGrowth = $avgGrowth / (count($historicalData) - 1);
        }
        
        // Projeção para os próximos 3 meses
        $lastValue = end($historicalData);
        $forecast = [];
        
        for ($i = 1; $i <= 3; $i++) {
            $projectedValue = $lastValue * pow(1 + $avgGrowth, $i);
            $forecast[] = [
                'month' => Carbon::now()->addMonths($i)->format('M/Y'),
                'projected_ngr' => max(0, $projectedValue),
                'projected_commission' => max(0, $projectedValue * 0.40), // Mostra 40%
            ];
        }
        
        return [
            'growth_rate' => $avgGrowth * 100,
            'projections' => $forecast,
        ];
    }
    
}