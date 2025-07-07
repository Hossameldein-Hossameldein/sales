<x-filament::page>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-black shadow rounded-xl p-4">
            <div class="text-gray-600 text-sm">عدد المنتجات</div>
            <div class="text-xl font-bold">{{ \App\Models\Product::count() }}</div>
        </div>

        <div class="bg-black shadow rounded-xl p-4">
            <div class="text-gray-600 text-sm">إجمالي الكمية</div>
            <div class="text-xl font-bold">{{ number_format(\App\Models\Product::sum('stock'), 2) }}</div>
        </div>

        <div class="bg-black shadow rounded-xl p-4">
            <div class="text-gray-600 text-sm">قيمة المخزون</div>
            <div class="text-xl font-bold">
                {{ number_format(\App\Models\Product::all()->sum(fn($p) => $p->stock * $p->purchase_price), 2) }} ج.م
            </div>
        </div>
    </div>

    {{-- جدول الجرد --}}
    {{ $this->table }}
</x-filament::page>
