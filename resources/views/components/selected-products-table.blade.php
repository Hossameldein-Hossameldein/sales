<table class="w-full text-sm border border-gray-200 dark:border-gray-700 rounded-lg">
    <thead class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
        <tr>
            <th class="px-3 py-2">المنتج</th>
            <th class="px-3 py-2">الباركود</th>
            <th class="px-3 py-2">السعر</th>
            <th class="px-3 py-2">الكمية</th>
            <th class="px-3 py-2">الإجمالي</th>
            <th class="px-3 py-2">إجراء</th>
        </tr>
    </thead>
    <tbody>
        @php
            $products = $this->formData['selected_products'] ?? [];
        @endphp

        @foreach ($products as $index => $product)
            <tr>
                <td class="px-3 py-2 text-center">{{ $product['name'] }}</td>
                <td class="px-3 py-2 text-center">{{ $product['barcode'] }}</td>
                <td class="px-3 py-2 text-center">{{ $product['price'] }}</td>
                <td class="px-3 py-2 text-center">
                    <input type="number" wire:model.lazy="formData.selected_products.{{ $index }}.quantity"
                        class="w-16 border rounded p-1 text-center">
                </td>
                <td class="px-3 py-2 text-center">
                    {{ number_format($product['price'] * $product['quantity'], 2) }}
                </td>
                <td class="px-3 py-2 text-center">
                    <button wire:click="removeProduct({{ $index }})"
                        class="text-red-500 hover:underline">حذف</button>
                </td>
            </tr>
        @endforeach
        @if (count($products))
    <tfoot>
        <tr class="bg-gray-100 font-bold">
            <td colspan="4" class="px-3 py-2 text-end">الإجمالي:</td>
            <td class="px-3 py-2 text-center text-green-600" wire:model="formData.total">
                {{ number_format($this->formData['total'] ?? 0, 2) }}
            </td>
            <td></td>
        </tr>
    </tfoot>
    @endif
    </tbody>
</table>
