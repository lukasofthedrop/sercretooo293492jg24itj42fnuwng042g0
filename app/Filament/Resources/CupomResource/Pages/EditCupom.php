<?php

namespace App\Filament\Resources\CupomResource\Pages;

use App\Filament\Resources\CupomResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCupom extends EditRecord
{
    protected static string $resource = CupomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
