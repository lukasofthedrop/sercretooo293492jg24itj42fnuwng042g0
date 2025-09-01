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
            
            Stat::make(new HtmlString('<span style="color: white;">TOTAL DE DEPOSITOS</span>'), \Helper::amountFormatDecimal($totalDepositsAfterDiscount))
                ->description(new HtmlString('<span style="color: white;">Total de depósitos</span>'))
                ->descriptionIcon('heroicon-o-banknotes')
                ->chart([25, 35, 30, 40, 45, 55, 60])
                ->chartColor('rgba(0, 128, 0, 0.5)'), // Verde para o lucro
            
            Stat::make(new HtmlString('<span style="color: white;">TOTAL DE SAQUES</span>'), \Helper::amountFormatDecimal($sumWithdrawalMonth))
                ->description(new HtmlString('<span style="color: white;">Total de Saques</span>'))
                ->descriptionIcon('heroicon-o-arrow-down-circle')
                ->chart([25, 35, 30, 40, 45, 55, 60])
                ->chartColor('rgba(255, 0, 0, 0.5)'), // Vermelho para os saques
            
            Stat::make(new HtmlString('<span style="color: white;">DENUNCIAS (Manutençao)</span>'), "0")
                ->description(new HtmlString('<span style="color: white;">Manutençao</span>'))
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->chart([25, 35, 30, 40, 45, 55, 60])
                ->chartColor('rgba(255, 215, 0, 0.5)'), // Dourado para o saldo PlayFiver
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
