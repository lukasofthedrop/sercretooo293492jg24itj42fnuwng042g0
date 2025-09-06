<?php
namespace App\Livewire;

use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\GamesKey;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\HtmlString;

class WalletOverview extends BaseWidget
{
    protected static ?int $sort = -2;
    use InteractsWithPageFilters;

    /**
     * @return array|Stat[]
     */
    protected function getStats(): array
    {
        $startDate = $this->filters['startDate'] ?? null;
        $endDate = $this->filters['endDate'] ?? null;
    
        $setting = \Helper::getSetting();
        $depositQuery = Deposit::query();
        $withdrawalQuery = Withdrawal::query();
    
        if (empty($startDate) && empty($endDate)) {
            $depositQuery->whereMonth('created_at', Carbon::now()->month);
        } else {
            $depositQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
    
        $sumDepositMonth = $depositQuery
            ->where('status', 1)
            ->sum('amount');
    
        $discountValue = GamesKey::sum('saldo_agente');
        $totalDepositsAfterDiscount = $sumDepositMonth;
    
        $withdrawalQuery->where('status', 1);
    
        if (empty($startDate) && empty($endDate)) {
            $withdrawalQuery->whereMonth('created_at', Carbon::now()->month);
        } else {
            $withdrawalQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
    
        $sumWithdrawalMonth = $withdrawalQuery->sum('amount');
    
        return [
            
            Stat::make('TOTAL DE DEPÓSITOS', \Helper::amountFormatDecimal($totalDepositsAfterDiscount))
                ->description('💰 Entrada de capital na plataforma')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success')
                ->chart([10, 30, 55, 75, 85, 95, 100])
                ->chartColor('#22c55e'), // Verde LucrativaBet
            
            Stat::make('TOTAL DE SAQUES', \Helper::amountFormatDecimal($sumWithdrawalMonth))
                ->description('💳 Saques processados com sucesso')
                ->descriptionIcon('heroicon-o-arrow-down-circle')
                ->color('danger')
                ->chart([100, 80, 60, 45, 30, 20, 10])
                ->chartColor('#ef4444'), // Vermelho para saques
            
            Stat::make('SISTEMA OPERACIONAL', "✅ ATIVO")
                ->description('🛡️ Monitoramento 24/7 ativo')
                ->descriptionIcon('heroicon-o-shield-check')
                ->color('info')
                ->chart([100, 100, 100, 100, 100, 100, 100])
                ->chartColor('#3b82f6'), // Azul para sistema
        ];
    }

    /**
     * @return bool
     */
    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    protected function getView(): string
    {
        return 'filament.widgets.stats-overview-widget';
    }

    protected function getWidgetWrapperClass(): string
    {
        return 'bg-black text-white';
    }
}
