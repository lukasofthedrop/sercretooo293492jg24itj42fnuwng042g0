<?php

namespace App\Filament\Resources\DailyBonusConfigResource\Pages;

use App\Filament\Resources\DailyBonusConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDailyBonusConfigs extends ListRecords
{
    protected static string $resource = DailyBonusConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
