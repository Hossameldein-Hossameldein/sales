<x-filament::page>
    <form wire:submit.prevent="loadReturns" class="space-y-4">
        {{ $this->form }}
    </form>

    <div class="mt-6 space-y-6">
        @forelse ($returns as $return)
            <div class="rounded-xl p-4 bg-white dark:bg-gray-900 ring-1 ring-gray-300 dark:ring-gray-700">
                <div class="flex justify-between text-sm text-gray-700 dark:text-gray-300 mb-2 flex-wrap gap-2">
                    <div>رقم الفاتورة المرتجع منها: <strong>{{ $return->invoice->invoice_number ?? '—' }}</strong></div>
                    <div>الموظف: <strong>{{ $return->user->name ?? '—' }}</strong></div>
                    <div>التاريخ: <strong>{{ $return->created_at->format('Y-m-d') }}</strong></div>
                </div>

                <div class="text-sm text-gray-800 dark:text-gray-100">
                    <div>إجمالي المرتجع: <span class="font-bold text-red-600 dark:text-red-400">{{ number_format($return->total, 2) }} ج.م</span></div>
                    @if ($return->notes)
                        <div class="mt-1">ملاحظات: {{ $return->notes }}</div>
                    @endif
                </div>

                <div class="mt-4">
                    <h4 class="text-sm font-semibold mb-2 text-gray-700 dark:text-gray-200">المنتجات المرتجعة:</h4>
                    <ul class="list-disc ps-5 text-sm text-gray-700 dark:text-gray-300 space-y-1">
                        @foreach ($return->items as $item)
                            <li>
                                {{ $item->product_name }} — {{ $item->quantity }} × {{ $item->price }} = 
                                <strong class="text-red-600 dark:text-red-400">{{ number_format($item->total, 2) }} ج.م</strong>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @empty
            <div class="text-center py-10 text-gray-500 dark:text-gray-400">
                لا توجد مرتجعات في الفترة المحددة.
            </div>
        @endforelse
    </div>
</x-filament::page>
