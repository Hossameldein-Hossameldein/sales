<x-filament::page>
    {{ $this->form }}

    @if ($productId)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 my-6">

            <div class="rounded-xl p-4 bg-gray-100 dark:bg-gray-800">
                <div class="text-sm text-gray-500 dark:text-gray-300">عدد مرات الشراء</div>
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    {{ $analytics['purchases'] ?? 0 }}
                </div>
            </div>

            <div class="rounded-xl p-4 bg-gray-100 dark:bg-gray-800">
                <div class="text-sm text-gray-500 dark:text-gray-300">عدد مرات البيع</div>
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                    {{ $analytics['sales'] ?? 0 }}
                </div>
            </div>

            <div class="rounded-xl p-4 bg-gray-100 dark:bg-gray-800">
                <div class="text-sm text-gray-500 dark:text-gray-300">عدد المرتجعات</div>
                <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                    {{ $analytics['returns'] ?? 0 }}
                </div>
            </div>

            <div class="rounded-xl p-4 bg-gray-100 dark:bg-gray-800">
                <div class="text-sm text-gray-500 dark:text-gray-300">المخزون الحالي</div>
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                    {{ $analytics['stock'] ?? 0 }}
                </div>
            </div>

        

        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            {{-- جدول الشراء --}}
            <div>
                <h3 class="text-lg font-bold mb-2 text-primary-600">المشتريات</h3>
                <table class="min-w-full bg-white dark:bg-gray-800 text-sm rounded-lg overflow-hidden shadow">
                    <thead>
                        <tr class="bg-gray-200 dark:bg-gray-700">
                            <th class="p-2 text-right">رقم الفاتورة</th>
                            <th class="p-2 text-right">التاريخ</th>
                            <th class="p-2 text-right">الكمية</th>
                            <th class="p-2 text-right">سعر الشراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($purchases as $item)
                            <tr class="border-t dark:border-gray-700">
                                <td class="p-2">
                                    <a href="{{ \App\Filament\Resources\PurchaseInvoiceResource::getUrl('view', ['record' => $item->purchase_invoice_id]) }}"
                                        target="_blank" class="text-blue-600 hover:underline">
                                        {{ $item->invoice->invoice_number ?? '-' }}
                                    </a>
                                </td>
                                <td class="p-2">{{ $item->invoice->date ?? '-' }}</td>
                                <td class="p-2">{{ $item->quantity }}</td>
                                <td class="p-2">{{ $item->purchase_price }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-gray-500 dark:text-gray-300">لا يوجد
                                    بيانات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- جدول البيع --}}
            <div>
                <h3 class="text-lg font-bold mb-2 text-primary-600">المبيعات</h3>
                <table class="min-w-full bg-white dark:bg-gray-800 text-sm rounded-lg overflow-hidden shadow">
                    <thead>
                        <tr class="bg-gray-200 dark:bg-gray-700">
                            <th class="p-2 text-right">رقم الفاتورة</th>
                            <th class="p-2 text-right">التاريخ</th>
                            <th class="p-2 text-right">الكمية</th>
                            <th class="p-2 text-right">سعر البيع</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sales as $item)
                            <tr class="border-t dark:border-gray-700">
                                <td class="p-2">
                                    <a href="{{ \App\Filament\Resources\SalesInvoiceResource::getUrl('view', ['record' => $item->sales_invoice_id]) }}"
                                        target="_blank" class="text-blue-600 hover:underline">
                                        {{ $item->invoice->invoice_number ?? '-' }}
                                    </a>
                                </td>
                                <td class="p-2">{{ $item->invoice->date ?? '-' }}</td>
                                <td class="p-2">{{ $item->quantity }}</td>
                                <td class="p-2">{{ $item->price }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-gray-500 dark:text-gray-300">لا يوجد
                                    بيانات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- جدول المرتجعات --}}
            <div>
                <h3 class="text-lg font-bold mb-2 text-primary-600">مرتجعات البيع</h3>
                <table class="min-w-full bg-white dark:bg-gray-800 text-sm rounded-lg overflow-hidden shadow">
                    <thead>
                        <tr class="bg-gray-200 dark:bg-gray-700">
                            <th class="p-2 text-right">رقم الفاتورة</th>
                            <th class="p-2 text-right">التاريخ</th>
                            <th class="p-2 text-right">الكمية المرتجعة</th>
                            <th class="p-2 text-right">السعر</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($returns as $item)
                            <tr class="border-t dark:border-gray-700">
                                <td class="p-2">
                                    <a href="{{ \App\Filament\Resources\SalesReturnResource::getUrl('view', ['record' => $item->sales_return_id]) }}"
                                        target="_blank" class="text-blue-600 hover:underline">
                                        {{ $item->return->id ?? '-' }}
                                    </a>
                                </td>
                                <td class="p-2">{{ $item->return->created_at->format('Y-m-d') ?? '-' }}</td>
                                <td class="p-2">{{ $item->quantity }}</td>
                                <td class="p-2">{{ $item->price }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-3 text-gray-500 dark:text-gray-300">لا يوجد
                                    مرتجعات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    @endif
</x-filament::page>
