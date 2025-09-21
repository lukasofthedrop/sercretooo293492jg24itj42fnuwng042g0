<?php

namespace App\Filament\Resources\MinesConfigResource\Pages;

use App\Filament\Resources\MinesConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMinesConfigs extends ListRecords
{
    protected static string $resource = MinesConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
