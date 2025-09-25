<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Formulário para criar/editar chaves -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow p-6">
            <form wire:submit="submit">
                {{ $this->form }}

                <div class="mt-6 flex items-center gap-4">
                    <x-filament::button type="submit">
                        {{ $editingKey ? 'Atualizar Chave' : 'Criar Chave' }}
                    </x-filament::button>
                    
                    @if($editingKey)
                        <x-filament::button 
                            type="button" 
                            color="gray"
                            wire:click="$set('editingKey', null)"
                        >
                            Cancelar
                        </x-filament::button>
                    @endif
                </div>
            </form>
        </div>

        <!-- Lista de chaves existentes -->
        <div class="bg-white dark:bg-gray-900 rounded-lg shadow">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Chaves de Licença Cadastradas
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Gerencie suas chaves de licença para clientes whitelabel
                </p>
            </div>
            
            <div class="p-6">
                @if($this->keys->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-400">
                                <tr>
                                    <th class="px-6 py-3">Client ID</th>
                                    <th class="px-6 py-3">Token</th>
                                    <th class="px-6 py-3">Endpoint</th>
                                    <th class="px-6 py-3">Timeout</th>
                                    <th class="px-6 py-3">Criado em</th>
                                    <th class="px-6 py-3">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
                                @foreach($this->keys as $key)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                            {{ $key->client_id }}
                                        </td>
                                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                            <span class="font-mono text-xs">
                                                {{ Str::limit($key->token, 20) }}...
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                            {{ $key->endpoint_base }}
                                        </td>
                                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                            {{ $key->timeout }}s
                                        </td>
                                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                            {{ $key->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-2">
                                                <x-filament::button 
                                                    size="sm" 
                                                    color="gray"
                                                    wire:click="edit({{ $key->id }})"
                                                >
                                                    Editar
                                                </x-filament::button>
                                                
                                                <x-filament::button 
                                                    size="sm" 
                                                    color="danger"
                                                    wire:click="delete({{ $key->id }})"
                                                    wire:confirm="Tem certeza que deseja excluir esta chave?"
                                                >
                                                    Excluir
                                                </x-filament::button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="text-gray-400 dark:text-gray-600 text-sm">
                            <svg class="w-12 h-12 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            <p class="text-lg font-medium">Nenhuma chave cadastrada</p>
                            <p class="text-sm">Crie sua primeira chave de licença acima</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>