<?php

namespace App\Services;

use App\Models\User;
use App\Models\AffiliateHistory;
use App\Models\AffiliateSettings;
use App\Models\Order;
use App\Models\Deposit;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AffiliateMetricsService
{
    /**
     * Calcula o NGR (Net Gaming Revenue) de um afiliado
     * NGR = Total de Depósitos - Total de Saques - Total de Bônus
     */
    public static function calculateNGR($affiliateId, $startDate = null, $endDate = null)
    {
        $settings = AffiliateSettings::getOrCreateForUser($affiliateId);
        
        // Define período baseado na configuração
        if (!$startDate || !$endDate) {
            switch ($settings->calculation_period) {
                case 'daily':
                    $startDate = Carbon::today();
                    $endDate = Carbon::today()->endOfDay();
                    break;
                case 'weekly':
                    $startDate = Carbon::now()->startOfWeek();
                    $endDate = Carbon::now()->endOfWeek();
                    break;
                case 'monthly':
                default:
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    break;
            }
        }
        
        // Pega todos os indicados do afiliado
        $referredUsers = User::where('inviter', $affiliateId)->pluck('id');
        
        if ($referredUsers->isEmpty()) {
            return [
                'ngr' => 0,
                'total_deposits' => 0,
                'total_withdrawals' => 0,
                'total_bonuses' => 0,
                'qualified' => false
            ];
        }
        
        // Calcula total de depósitos
        $totalDeposits = Deposit::whereIn('user_id', $referredUsers)
            ->where('status', 1)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
        
        // Calcula total de saques
        $totalWithdrawals = Withdrawal::whereIn('user_id', $referredUsers)
            ->where('status', 1)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
        
        // Calcula total de bônus (considerando como valor dado ao jogador)
        // Por enquanto, vamos considerar 0 até verificarmos a estrutura correta
        $totalBonuses = 0;
        
        // Cálculo do NGR
        $ngr = $totalDeposits - $totalWithdrawals - abs($totalBonuses);
        
        // Verifica se atingiu o NGR mínimo
        $qualified = $ngr >= $settings->ngr_minimum;
        
        return [
            'ngr' => $ngr,
            'total_deposits' => $totalDeposits,
            'total_withdrawals' => $totalWithdrawals,
            'total_bonuses' => abs($totalBonuses),
            'qualified' => $qualified,
            'minimum_required' => $settings->ngr_minimum,
            'period' => $settings->calculation_period,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d')
        ];
    }
    
    /**
     * Calcula comissão baseada nas configurações personalizadas
     */
    public static function calculateCommission($affiliateId, $type = 'revshare', $baseValue = 0)
    {
        $settings = AffiliateSettings::getOrCreateForUser($affiliateId);
        
        if (!$settings->is_active) {
            return 0;
        }
        
        if ($type === 'revshare') {
            return ($baseValue * $settings->revshare_percentage) / 100;
        } elseif ($type === 'cpa') {
            // Verifica se o NGR foi atingido para qualificar para CPA
            $ngrData = self::calculateNGR($affiliateId);
            if ($ngrData['qualified']) {
                return $settings->cpa_value;
            }
            return 0;
        }
        
        return 0;
    }
    
    /**
     * Retorna métricas consolidadas do afiliado
     */
    public static function getAffiliateMetrics($affiliateId)
    {
        $settings = AffiliateSettings::getOrCreateForUser($affiliateId);
        $ngrData = self::calculateNGR($affiliateId);
        
        // Total de indicados
        $totalReferred = User::where('inviter', $affiliateId)->count();
        
        // Total de indicados ativos (que fizeram pelo menos 1 depósito)
        $referredIds = User::where('inviter', $affiliateId)->pluck('id');
        $activeReferred = Deposit::whereIn('user_id', $referredIds)
            ->where('status', 1)
            ->distinct('user_id')
            ->count('user_id');
        
        // Total de comissões ganhas
        $totalCommissions = AffiliateHistory::where('inviter', $affiliateId)
            ->where('status', 1)
            ->sum('commission');
        
        // Comissões pendentes
        $pendingCommissions = AffiliateHistory::where('inviter', $affiliateId)
            ->where('status', 0)
            ->sum('commission');
        
        return [
            'tier' => $settings->tier,
            'is_active' => $settings->is_active,
            'total_referred' => $totalReferred,
            'active_referred' => $activeReferred,
            'conversion_rate' => $totalReferred > 0 ? round(($activeReferred / $totalReferred) * 100, 2) : 0,
            'ngr' => $ngrData,
            'total_commissions' => $totalCommissions,
            'pending_commissions' => $pendingCommissions,
            'revshare_percentage' => $settings->revshare_percentage,
            'revshare_display' => $settings->revshare_display,
            'cpa_value' => $settings->cpa_value,
            'calculation_period' => $settings->calculation_period
        ];
    }
}