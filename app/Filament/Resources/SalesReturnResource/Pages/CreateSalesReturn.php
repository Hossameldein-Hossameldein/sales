<?php

namespace App\Filament\Resources\SalesReturnResource\Pages;

use App\Filament\Resources\SalesReturnResource;
use App\Models\Product;
use App\Models\SalesReturnItem;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateSalesReturn extends CreateRecord
{
    protected static string $resource = SalesReturnResource::class;

    public ?array $data = null;
    public function mutateFormDataBeforeCreate(array $data): array
    {
        $this->data = $data;
        $total = 0;
        foreach ($data['items'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        $data['total'] = $total;
        $data['user_id'] = auth()->id();

        return $data;
    }
    public function afterCreate(): void
    {
        foreach ($this->data['items'] as $item) {
            $product = Product::find($item['product_id']);
            SalesReturnItem::create([
                'sales_return_id' => $this->record->id,
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'barcode' => $product->barcode,
                'product_name' => $product->name,
                'price' => $item['price'],
                'total' => $product->quantity * $item['price'],
            ]);
            $product->stock -= $product->quantity;
            $product->save();
        }
    }
}
