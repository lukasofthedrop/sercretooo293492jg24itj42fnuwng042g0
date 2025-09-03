<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class Top5GamesCircularWidget extends Widget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '30s';
    protected static bool $isLazy = true;

    protected function getViewData(): array
    {
        return [];
    }

    protected static string $view = 'filament.widgets.top5-games-circular-widget';

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}