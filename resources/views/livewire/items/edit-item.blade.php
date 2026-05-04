<div>
    <form wire:submit="save">
        {{ $this->form }}
        <x-filament::button type="submit" size="lg" class="mt-4" icon="heroicon-m-sparkles">
            Update
        </x-filament::button>
    </form>

    <x-filament-actions::modals />
</div>
