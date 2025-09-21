<?php

namespace App\Filament\Resources\GameOpenConfigResource\Pages;

use App\Filament\Resources\GameOpenConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGameOpenConfigs extends ListRecords
{
    protected static string $resource = GameOpenConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
