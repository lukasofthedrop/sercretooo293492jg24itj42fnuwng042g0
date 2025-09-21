<?php

namespace App\Filament\Resources\MinesConfigResource\Pages;

use App\Filament\Resources\MinesConfigResource;
use App\Models\GameConfig;
use Filament\Resources\Pages\EditRecord;

class EditMinesConfig extends EditRecord
{
    protected static string $resource = MinesConfigResource::class;

    public function mount($record = null): void
    {
        // Tenta carregar o único registro da configuração
        $found = GameConfig::first();

        // Se não existir, cria um registro com valores padrão
        if (!$found) {
            $found = GameConfig::create([
                'bombs_count'             => 5,          // se este campo for necessário, ou remova se não for usado
                'min_bet'                 => 1,
                'max_bet'                 => 100,
                'meta_arrecadacao'        => 0,
                'percentual_distribuicao' => 0,
                'modo_atual'              => 'arrecadacao',
                'total_arrecadado'        => 0,
                'total_distribuido'       => 0,
                'minas_distribuicao'      => 5,
                'minas_arrecadacao'       => 5,
                'x_por_mina'              => 0.10,
                'x_a_cada_5'              => 1.50,
                'bet_loss'                => 50,
                'modo_influenciador'      => false,
                'modo_perdedor'           => false,
                'start_cycle_at'          => now(),
            ]);
        }

        // Define o registro a ser editado
        $record = $found->id;

        parent::mount($record);
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
