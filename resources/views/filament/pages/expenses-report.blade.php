<x-filament::page>
    <form wire:submit.prevent="generateReport">
        {{ $this->form }}
    </form>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
        <div class="rounded-xl p-4 bg-gray-100 dark:bg-gray-800">
            <div class="text-sm text-gray-500 dark:text-gray-300">عدد المصروفات</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $data['count'] ?? 0 }}</div>
        </div>

        <div class="rounded-xl p-4 bg-gray-100 dark:bg-gray-800">
            <div class="text-sm text-gray-500 dark:text-gray-300">إجمالي المصروفات</div>
            <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                {{ number_format($data['total_expenses'] ?? 0, 2) }} ج.م
            </div>
        </div>
    </div>
</x-filament::page>
