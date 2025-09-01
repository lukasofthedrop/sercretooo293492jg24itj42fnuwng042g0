<?php

namespace App\Filament\Widgets;

use App\Models\AffiliateHistory;
use App\Models\Order;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Helpers\Core as Helper;

class StatsUserDetailOverview extends BaseWidget
{

    public User $record;

    public function mount($record)
    {
       $this->record = $record;
    }

    /**
     * @return array|Stat[]
     */ 
    protected function getStats(): array
    {
        $totalGanhos = Order::where('user_id', $this->record->id)->where('type', 'win')->sum('amount');
        $totalPerdas = Order::where('user_id', $this->record->id)->where('type', 'bet')->sum('amount');
        $totalAfiliados = AffiliateHistory::where('inviter', $this->record->id)->sum('commission_paid');

        // Novos widgets
        $trouxeDeDepositantes = AffiliateHistory::where('inviter', $this->record->id)
            ->where('status', 1)
            ->count();

        $trouxeDeLucro = AffiliateHistory::where('inviter', $this->record->id)
            ->where('status', 1)
            ->sum('deposited');

        $trouxeDeClientes = AffiliateHistory::where('inviter', $this->record->id)
            ->whereIn('status', [0, 1])
            ->count();

        return [
            Stat::make('TOTAL DE GANHO', Helper::amountFormatDecimal(Helper::formatNumber($totalGanhos))) 
                ->description('Total de ganhos das apostas')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([10, 20, 15, 30, 25, 40, 35])
                ->chartColor('rgba(59, 130, 246, 0.5)'),

            Stat::make('TOTAL DE PERCAS', Helper::amountFormatDecimal(Helper::formatNumber($totalPerdas)))
                ->description('Total de perdas das apostas')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->chart([10, 20, 15, 30, 25, 40, 35])
                ->chartColor('rgba(59, 130, 246, 0.5)'),

            Stat::make('GANHO COMO AFILIADO', Helper::amountFormatDecimal(Helper::formatNumber($totalAfiliados)))
                ->description('Total de ganhos como afiliado') 
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([10, 20, 15, 30, 25, 40, 35])
                ->chartColor('rgba(59, 130, 246, 0.5)'),

            // Novo widget: Trouxe de Depositantes
            Stat::make('TROUXE DE DEPOSITANTES', $trouxeDeDepositantes)
                ->description('Quantidade de depositantes trazidos')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([10, 20, 15, 30, 25, 40, 35])
                ->chartColor('rgba(59, 130, 246, 0.5)'),

            // Novo widget: Trouxe de Lucro
            Stat::make('TROUXE DE LUCRO', Helper::amountFormatDecimal(Helper::formatNumber($trouxeDeLucro)))
                ->description('Quantidade de lucro trazido')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('success')
                ->chart([10, 20, 15, 30, 25, 40, 35])
                ->chartColor('rgba(59, 130, 246, 0.5)'),

            // Novo widget: Trouxe de Clientes
            Stat::make('TROUXE DE CLIENTES', $trouxeDeClientes)
                ->description('Total de clientes trazidos')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([10, 20, 15, 30, 25, 40, 35])
                ->chartColor('rgba(59, 130, 246, 0.5)'),
        ];
    }
}
