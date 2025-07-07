<x-filament::page>
    <form wire:submit.prevent="generateReport">
        {{ $this->form }}
    </form>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 my-6">
        <div class="rounded-xl p-4 bg-gray-100 dark:bg-gray-800">
            <div class="text-sm text-gray-500 dark:text-gray-300">عدد فواتير البيع</div>
            <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $data['invoice_count'] ?? 0 }}</div>
        </div>

        <div class="rounded-xl p-4 bg-gray-100 dark:bg-gray-800">
            <div class="text-sm text-gray-500 dark:text-gray-300">إجمالي المبيعات</div>
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                {{ number_format($data['total_sales'] ?? 0, 2) }} ج.م
            </div>
        </div>

        <div class="rounded-xl p-4 bg-gray-100 dark:bg-gray-800">
            <div class="text-sm text-gray-500 dark:text-gray-300">إجمالي الخصومات</div>
            <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                {{ number_format($data['total_discount'] ?? 0, 2) }} ج.م
            </div>
        </div>

        <div class="rounded-xl p-4 bg-gray-100 dark:bg-gray-800">
            <div class="text-sm text-gray-500 dark:text-gray-300">إجمالي الضريبة</div>
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                {{ number_format($data['total_tax'] ?? 0, 2) }} ج.م
            </div>
        </div>

        <div class="rounded-xl p-4 bg-gray-100 dark:bg-gray-800">
            <div class="text-sm text-gray-500 dark:text-gray-300">مبيعات القطاعي</div>
            <div class="text-2xl font-bold">{{ number_format($data['retail_sales'] ?? 0, 2) }} ج.م</div>
        </div>

        <div class="rounded-xl p-4 bg-gray-100 dark:bg-gray-800">
            <div class="text-sm text-gray-500 dark:text-gray-300">مبيعات الجملة</div>
            <div class="text-2xl font-bold">{{ number_format($data['wholesale_sales'] ?? 0, 2) }} ج.م</div>
        </div>
    </div>
</x-filament::page>
