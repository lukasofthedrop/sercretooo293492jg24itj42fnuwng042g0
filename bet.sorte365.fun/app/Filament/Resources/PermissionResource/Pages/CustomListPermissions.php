<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PermissionResource;

class CustomListPermissions extends ListRecords
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        // Suas ações de cabeçalho personalizadas
    }
}
