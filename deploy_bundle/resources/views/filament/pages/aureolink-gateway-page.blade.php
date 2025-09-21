<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Stats Widgets -->
        @if (count($this->getHeaderWidgets()))
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 xl:grid-cols-4">
                @foreach ($this->getHeaderWidgets() as $widget)
                    @livewire($widget, ['lazy' => true])
                @endforeach
            </div>
        @endif

        <!-- Configuration Form -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <form wire:submit="submit">
                    {{ $this->form }}
                    
                    <div class="mt-6 flex items-center justify-end space-x-3">
                        <x-filament::button
                            type="button"
                            wire:click="testConnection"
                            color="info"
                        >
                            Testar Conexão
                        </x-filament::button>
                        
                        <x-filament::button
                            type="submit"
                            color="primary"
                        >
                            Salvar Configurações
                        </x-filament::button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Histórico de Transações AureoLink
                </h3>
                
                {{ $this->table }}
            </div>
        </div>
    </div>
</x-filament-panels::page> 