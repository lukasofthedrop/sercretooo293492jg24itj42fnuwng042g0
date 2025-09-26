<?php

namespace App\Filament\Resources\DailyBonusConfigResource\Pages;

use App\Filament\Resources\DailyBonusConfigResource;
use App\Models\DailyBonusConfig;
use Filament\Resources\Pages\EditRecord;

class EditDailyBonusConfig extends EditRecord
{
    protected static string $resource = DailyBonusConfigResource::class;

    /**
     * Monta a página. Carrega o único registro ou cria se não existir.
     */
    public function mount($record = null): void
    {
        // Procura o primeiro (e único) registro
        $found = DailyBonusConfig::first();

        if (!$found) {
            // Se não houver, cria com valores padrão
            $found = DailyBonusConfig::create([
                'bonus_value' => 10.00,
                'cycle_hours' => 24,
            ]);
        }

        // agora define $record como o ID encontrado
        $record = $found->id;

        parent::mount($record);
    }

    /**
     * Ao salvar, redireciona para a mesma página
     */
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
