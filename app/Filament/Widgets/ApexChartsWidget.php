<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Livewire\Attributes\On;

class ApexChartsWidget extends Widget
{
    protected static ?int $sort = 1;
    protected static string $view = 'filament.widgets.apex-charts-widget';
    protected static ?string $pollingInterval = null; // JS cuida da atualização
    protected int | string | array $columnSpan = 'full';
    
    public static function canView(): bool
    {
        // Sempre mostrar o widget se estiver no painel admin
        return true;
    }
    
    /**
     * Método chamado após o componente ser montado
     */
    public function mount(): void
    {
        // Disparar JavaScript após o componente estar pronto
        $this->dispatch('load-charts');
    }
    
    /**
     * Método chamado pelo wire:init para inicializar os gráficos
     */
    public function initializeCharts(): void
    {
        // Emitir evento JavaScript para inicializar os gráficos
        $this->dispatch('load-charts');
    }
    
    protected function getViewData(): array
    {
        return [
            'apiEndpoint' => '/api/admin/dashboard-metrics'
        ];
    }
}