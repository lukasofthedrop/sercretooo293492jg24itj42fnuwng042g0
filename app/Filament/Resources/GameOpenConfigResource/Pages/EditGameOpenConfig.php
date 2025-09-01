<?php

namespace App\Filament\Resources\GameOpenConfigResource\Pages;

use App\Filament\Resources\GameOpenConfigResource;
use App\Models\GameOpenConfig;
use Filament\Resources\Pages\EditRecord;

class EditGameOpenConfig extends EditRecord
{
    protected static string $resource = GameOpenConfigResource::class;

    public function mount($record = null): void
    {
        // Carrega ou cria
        $found = GameOpenConfig::first();
        if (!$found) {
            $found = GameOpenConfig::create([
                'requires_deposit_today' => false,
            ]);
        }
        $record = $found->id;

        parent::mount($record);
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
