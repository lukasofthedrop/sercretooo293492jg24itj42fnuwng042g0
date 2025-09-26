<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class UsersRankingColumnWidget extends Widget
{
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = '60s';
    protected static bool $isLazy = true;

    protected function getViewData(): array
    {
        return [];
    }

    protected static string $view = 'filament.widgets.users-ranking-column-widget';

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}