<?php

namespace App\Filament\Resources\DistributionSystemResource\Pages;

use App\Filament\Resources\DistributionSystemResource;
use App\Models\DistributionSystem;
use Filament\Resources\Pages\EditRecord;

class EditDistributionSystem extends EditRecord
{
    protected static string $resource = DistributionSystemResource::class;

    /**
     * Ao montar a página, pegamos (ou criamos) o único registro.
     */
    public function mount($record = null): void
    {
        // Se já existir um registro, pega o primeiro
        $found = DistributionSystem::first();

        // Se não existir, cria
        if (! $found) {
            $found = DistributionSystem::create([
                'meta_arrecadacao' => 0,
                'percentual_distribuicao' => 0,
                'rtp_arrecadacao' => 0,
                'rtp_distribuicao' => 0,
                'total_arrecadado' => 0,
                'total_distribuido' => 0,
                'modo' => 'arrecadacao',
                'ativo' => false,
            ]);
        }

        // Força o $record a ser o ID do registro encontrado/criado
        $record = $found->id;

        // Agora chama o mount original do EditRecord
        parent::mount($record);
    }

    /**
     * Redireciona de volta para a própria rota (index) após salvar
     */
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
