<div class="relative">
    <input type="text" wire:model="search" wire:keyup="searchProducts" placeholder="اكتب اسم أو باركود منتج"
        class="w-full border border-gray-300 rounded p-2">

    @if (!empty($suggestions))
        <ul class="absolute bg-white border w-full z-10 mt-1 max-h-48 overflow-y-auto">
            @foreach ($suggestions as $product)
                <li wire:click="selectProduct({{ $product['id'] }})" class="p-2 hover:bg-gray-100 cursor-pointer">
                    {{ $product['name'] }} ({{ $product['barcode'] ?? 'بدون باركود' }})
                </li>
            @endforeach
        </ul>
    @endif
</div>
