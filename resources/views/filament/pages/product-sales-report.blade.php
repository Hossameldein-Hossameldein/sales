<x-filament::page>
    <form wire:submit.prevent="generateReport">
        {{ $this->form }}
    </form>

    <div class="mt-6 space-y-6">
        <h2 class="text-lg font-bold dark:text-white">أكثر 10 منتجات مبيعًا</h2>

        <div class="w-full overflow-auto rounded-xl ring-1 ring-gray-300 dark:ring-gray-700 bg-white dark:bg-gray-900">
            <table class="min-w-full w-full text-sm rtl:text-right divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-start">المنتج</th>
                        <th class="px-4 py-3 text-start">الكمية المباعة</th>
                        <th class="px-4 py-3 text-start">إجمالي المبيعات</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($topProducts as $product)
                        <tr class="bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100">
                            <td class="px-4 py-3">{{ $product->product_name }}</td>
                            <td class="px-4 py-3">{{ $product->total_quantity }}</td>
                            <td class="px-4 py-3 text-green-600 dark:text-green-400">
                                {{ number_format($product->total_sales, 2) }} ج.م
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-6 text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900">
                                <svg class="mx-auto h-10 w-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m3-7.5v.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="mt-2 text-sm">لا توجد بيانات للعرض في الفترة المحددة</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament::page>
