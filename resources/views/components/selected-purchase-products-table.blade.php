<table class="w-full text-sm border border-gray-200 dark:border-gray-700 rounded-lg mt-4">
    <thead class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
        <tr>
            <th class="px-2 py-2">اسم المنتج</th>
            <th class="px-2 py-2">الكمية</th>
            <th class="px-2 py-2">سعر الشراء</th>
            <th class="px-2 py-2">سعر البيع القطاعي</th>
            <th class="px-2 py-2">سعر الجملة</th>
            <th class="px-2 py-2">الإجمالي</th>
            <th class="px-2 py-2">إجراء</th>
        </tr>
    </thead>
    <tbody>
        @php $items = $this->formData['selected_products'] ?? []; @endphp

        @foreach ($items as $index => $item)
            <tr>
                <td class="px-2 py-1">{{ $item['product_name'] }}</td>
                <td class="px-2 py-1">
                    <input type="number" wire:model.defer="formData.selected_products.{{ $index }}.quantity"
                        class="w-16 border rounded p-1 text-center" min="1"
                        wire:change="updateTotal({{ $index }})">
                </td>
                <td class="px-2 py-1">
                    <input type="number"
                        wire:model.defer="formData.selected_products.{{ $index }}.purchase_price"
                        class="w-20 border rounded p-1 text-center" step="0.01"
                        wire:change="updateTotal({{ $index }})">
                </td>
                <td class="px-2 py-1">
                    <input type="number" wire:model.defer="formData.selected_products.{{ $index }}.retail_price"
                        class="w-20 border rounded p-1 text-center" step="0.01">
                </td>
                <td class="px-2 py-1">
                    <input type="number"
                        wire:model.defer="formData.selected_products.{{ $index }}.wholesale_price"
                        class="w-20 border rounded p-1 text-center" step="0.01">
                </td>
                <td class="px-2 py-1">
                    {{ number_format($item['quantity'] * $item['purchase_price'], 2) }}
                </td>
                <td class="px-2 py-1 text-center">
                    <button wire:click="removeProduct({{ $index }})"
                        class="text-red-600 hover:underline">حذف</button>
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="bg-gray-50 font-bold">
            <td colspan="5" class="text-center py-2">الإجمالي الكلي</td>
            <td colspan="2" class="text-center">
                {{ number_format(collect($items)->sum(fn($item) => $item['quantity'] * $item['purchase_price']), 2) }}
            </td>
        </tr>
    </tfoot>
</table>
