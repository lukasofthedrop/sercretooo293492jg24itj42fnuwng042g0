<x-filament-panels::page>
    <form wire:submit="submit">
        {{ $this->form }}
        <br>
        <x-filament::button type="submit" form="submit">
            Salvar
        </x-filament::button>
    </form>
</x-filament-panels::page> 