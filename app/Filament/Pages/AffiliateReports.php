<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;
use App\Models\AffiliateHistory;
use App\Models\AffiliateSettings;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Services\AffiliateMetricsService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AffiliateReports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Relatórios de Afiliados';
    protected static ?string $navigationGroup = 'GESTÃO DE AFILIADOS';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'minhas-conversoes';
    protected static ?string $title = 'Relatórios de Afiliados';
    
    protected static string $view = 'filament.pages.affiliate-reports';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public function mount(): void
    {
        // Dados serão carregados via Livewire
    }

    public function getMetrics(): array
    {
        // Métricas gerais do sistema de afiliados
        $totalAffiliates = User::whereNotNull('inviter_code')
            ->where('email', '!=', 'lucrativa@bet.com')
            ->count();

        $activeAffiliates = User::whereNotNull('inviter_code')
            ->where('email', '!=', 'lucrativa@bet.com')
            ->whereHas('wallet', function($q) {
                $q->where('refer_rewards', '>', 0);
            })
            ->count();

        $totalReferred = User::whereNotNull('inviter')->count();

        $totalCommissionsPaid = DB::table('wallets')
            ->whereIn('user_id', function($query) {
                $query->select('id')
                    ->from('users')
                    ->whereNotNull('inviter_code')
                    ->where('email', '!=', 'lucrativa@bet.com');
            })
            ->sum('refer_rewards');

        // NGR Total do mês
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();
        
        $totalDeposits = Deposit::whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', 1)
            ->sum('amount');
            
        $totalWithdrawals = Withdrawal::whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', 1)
            ->sum('amount');

        $totalNGR = $totalDeposits - $totalWithdrawals;

        return [
            'total_affiliates' => $totalAffiliates,
            'active_affiliates' => $activeAffiliates,
            'total_referred' => $totalReferred,
            'total_commissions' => $totalCommissionsPaid,
            'total_deposits' => $totalDeposits,
            'total_withdrawals' => $totalWithdrawals,
            'total_ngr' => $totalNGR,
            'conversion_rate' => $totalAffiliates > 0 ? round(($activeAffiliates / $totalAffiliates) * 100, 2) : 0
        ];
    }

    public function getTopAffiliates(): array
    {
        return User::whereNotNull('inviter_code')
            ->where('email', '!=', 'lucrativa@bet.com')
            ->with(['wallet'])
            ->limit(10)
            ->get()
            ->map(function($affiliate) {
                $settings = AffiliateSettings::getOrCreateForUser($affiliate->id);
                $ngrData = AffiliateMetricsService::calculateNGR($affiliate->id);
                $referredCount = User::where('inviter', $affiliate->id)->count();
                
                return [
                    'name' => $affiliate->name,
                    'email' => $affiliate->email,
                    'code' => $affiliate->inviter_code,
                    'referred_count' => $referredCount,
                    'balance' => $affiliate->wallet->refer_rewards ?? 0,
                    'ngr' => $ngrData['ngr'],
                    'ngr_qualified' => $ngrData['qualified'],
                    'tier' => $settings->tier,
                    'revshare_real' => $settings->revshare_percentage,
                    'revshare_display' => $settings->revshare_display,
                    'is_active' => $settings->is_active
                ];
            })
            ->toArray();
    }

    public function getMonthlyData(): array
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startDate = $date->copy()->startOfMonth();
            $endDate = $date->copy()->endOfMonth();
            
            $deposits = Deposit::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 1)
                ->sum('amount');
                
            $withdrawals = Withdrawal::whereBetween('created_at', [$startDate, $endDate])
                ->where('status', 1)
                ->sum('amount');
                
            $months[] = [
                'month' => $date->format('M/Y'),
                'deposits' => $deposits,
                'withdrawals' => $withdrawals,
                'ngr' => $deposits - $withdrawals
            ];
        }
        
        return $months;
    }

    public function getTierDistribution(): array
    {
        $tiers = DB::table('affiliate_settings')
            ->select('tier', DB::raw('count(*) as count'))
            ->groupBy('tier')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->tier => $item->count];
            })
            ->toArray();
            
        return [
            'bronze' => $tiers['bronze'] ?? 0,
            'silver' => $tiers['silver'] ?? 0,
            'gold' => $tiers['gold'] ?? 0,
            'custom' => $tiers['custom'] ?? 0
        ];
    }

    public function getRecentActivities(): array
    {
        return AffiliateHistory::with(['user', 'inviterUser'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function($history) {
                return [
                    'date' => $history->created_at->format('d/m/Y H:i'),
                    'inviter' => $history->inviterUser->name ?? 'N/A',
                    'user' => $history->user->name ?? 'N/A',
                    'type' => $history->commission_type,
                    'commission' => $history->commission,
                    'status' => $history->status ? 'Pago' : 'Pendente'
                ];
            })
            ->toArray();
    }
}