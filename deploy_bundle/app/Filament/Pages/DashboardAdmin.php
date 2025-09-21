<?php

namespace App\Filament\Pages;


use App\Filament\Widgets\StatsOverview;


use App\Livewire\WalletOverview;
use Illuminate\Support\HtmlString;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class DashboardAdmin extends \Filament\Pages\Dashboard
{
    use HasFiltersForm, HasFiltersAction;

    /**
     * @return string|\Illuminate\Contracts\Support\Htmlable|null
     */
    public function getSubheading(): string| null|\Illuminate\Contracts\Support\Htmlable
    {
        return "Bem-vindo(a) de volta, Admin! Seu painel está pronto para você.";
    }
    

    /**
     * @param Form $form
     * @return Form
     */
    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    /**
     * @return array|\Filament\Actions\Action[]|\Filament\Actions\ActionGroup[]
     */
    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->label('Filtro')
                ->form([
                    DatePicker::make('startDate')->label('Data Incial'),
                    DatePicker::make('endDate')->label('Data Final'),
                ]),
        ];
    }

    /**
     * Determine if the current user can view this page.
     *
     * @return bool
     */
    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin'); // Controla o acesso total à página
    }
    
    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin'); // Controla a visualização de elementos específicos
    }

    /**
     * @return string[]
     */
    public function getWidgets(): array
    {
        return [
            WalletOverview::class,
            StatsOverview::class,
        ];
    }
}
