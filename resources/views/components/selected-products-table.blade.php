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
            $products = $this->productsState ?? [];
        @endphp

        @foreach ($products as $index => $product)
            <tr>
                <td class="px-3 py-2 text-center">{{ $product['name'] }}</td>
                <td class="px-3 py-2 text-center">{{ $product['barcode'] }}</td>
                <td class="px-3 py-2 text-center">{{ $product['price'] }}</td>
                <td class="px-3 py-2 text-center">
                    <input type="number" wire:model="productsState.{{ $index }}.quantity" wire:change="$refresh"
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


            <td colspan="1" class="px-3 py-2 text-center">عدد المنتجات:
                {{ count($products) }}</td>
            <td colspan="1" class="px-3 py-2 text-center">عدد القطع:
                {{ collect($products)->sum(fn($product) => $product['quantity']) }}</td>

            <td colspan="1" class="px-3 py-2 text-center">الإجمالي:
                {{ collect($products)->sum(fn($product) => $product['price'] * $product['quantity']) - $this->formData['discount'] }}
            </td>
            <td colspan="1"></td>
            <td colspan="1"></td>
            <td colspan="1"></td>

        </tr>
    </tfoot>
    @endif
    </tbody>
</table>
