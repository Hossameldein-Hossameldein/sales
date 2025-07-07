<x-filament::page>
    <div class="space-y-6">
        {{ $this->form }}

        <x-filament::button wire:click="create" color="primary">
            حفظ الفاتورة
        </x-filament::button>
    </div>
</x-filament::page>
