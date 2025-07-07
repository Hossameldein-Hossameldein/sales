<x-filament::page>
    <form wire:submit.prevent="generateReport">
        {{ $this->form }}
    </form>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <div class="rounded-xl p-4 bg-gray-100 dark:bg-gray-800">
            <div class="text-sm text-gray-500 dark:text-gray-300">إجمالي المبيعات</div>
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                {{ number_format($data['sales'] ?? 0, 2) }} ج.م
            </div>
        </div>

        <div class="rounded-xl p-4 bg-gray-100 dark:bg-gray-800">
            <div class="text-sm text-gray-500 dark:text-gray-300">إجمالي المصروفات</div>
            <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                {{ number_format($data['expenses'] ?? 0, 2) }} ج.م
            </div>
        </div>

        <div class="rounded-xl p-4 bg-gray-100 dark:bg-gray-800">
            <div class="text-sm text-gray-500 dark:text-gray-300">صافي الربح</div>
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                {{ number_format($data['profit'] ?? 0, 2) }} ج.م
            </div>
        </div>
    </div>
</x-filament::page>
