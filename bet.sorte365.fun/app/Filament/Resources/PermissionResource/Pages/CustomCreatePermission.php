<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PermissionResource;

class CustomCreatePermission extends CreateRecord
{
    protected static string $resource = PermissionResource::class;
}
