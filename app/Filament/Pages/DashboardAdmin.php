<?php

namespace App\Filament\Pages;


use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\TopGamesOverview;
use App\Filament\Widgets\TopUsersOverview;

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
        return "Sistema operacional. Controle total da plataforma disponível.";
    }
    

    /**
     * @param Form $form
     * @return Form
     */
    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('DASHBOARD EXECUTIVO')
                ->description(new HtmlString('
                    <div style="font-weight: 600; display: flex; align-items: center;">
                        Painel de controle profissional. Gerencie todos os aspectos da plataforma com precisão.
                    </div>
            ')),
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
     * @return string[]
     */
    public function getWidgets(): array
    {
        return [
            WalletOverview::class,
            StatsOverview::class,
            TopGamesOverview::class,
            TopUsersOverview::class,
        ];
    }
}
