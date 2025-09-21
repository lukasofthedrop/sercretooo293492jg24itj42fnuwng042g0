<x-filament-panels::page>
    <form wire:submit="submit">
        {{ $this->form }}
        <br>
        <x-filament::button type="submit" form="submit">
            {{ $editingKey ? 'Atualizar' : 'Salvar' }}
        </x-filament::button>
        @if($editingKey)
            <x-filament::button color="secondary" type="button" wire:click="$set('editingKey', null)">
                Cancelar edição
            </x-filament::button>
        @endif
    </form>

    <hr class="my-6">

    <h3 class="text-lg font-bold mb-2">Chaves Cadastradas</h3>
    <table class="w-full text-left border">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2">Token</th>
                <th class="p-2">Client ID</th>
                <th class="p-2">Endpoint</th>
                <th class="p-2">Timeout</th>
                <th class="p-2">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($this->keys as $key)
                <tr class="border-t">
                    <td class="p-2">{{ $key->token }}</td>
                    <td class="p-2">{{ $key->client_id }}</td>
                    <td class="p-2">{{ $key->endpoint_base }}</td>
                    <td class="p-2">{{ $key->timeout }}</td>
                    <td class="p-2 flex gap-2">
                        <x-filament::button color="primary" size="sm" wire:click="edit({{ $key->id }})">Editar</x-filament::button>
                        <x-filament::button color="danger" size="sm" wire:click="delete({{ $key->id }})" onclick="return confirm('Tem certeza que deseja remover esta chave?')">Excluir</x-filament::button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="p-2 text-center">Nenhuma chave cadastrada.</td></tr>
            @endforelse
        </tbody>
    </table>
</x-filament-panels::page>