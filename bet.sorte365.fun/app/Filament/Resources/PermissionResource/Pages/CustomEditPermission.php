<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\PermissionResource;

class CustomEditPermission extends EditRecord
{
    protected static string $resource = PermissionResource::class;
}
