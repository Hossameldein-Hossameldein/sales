<?php

namespace App\Filament\Resources\SalesInvoiceResource\Pages;

use App\Filament\Resources\SalesInvoiceResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;


class CreateSalesInvoice extends CreateRecord
{
    protected static string $resource = SalesInvoiceResource::class;
    public static function beforeCreate(array $data): void
    {
        foreach ($data['items'] as $item) {
            $product = \App\Models\Product::find($item['product_id']);

            if (!$product) {
                throw ValidationException::withMessages([
                    'items' => "المنتج غير موجود.",
                ]);
            }

            if ($product->stock < $item['quantity']) {
                throw ValidationException::withMessages([
                    'items' => "الكمية المطلوبة للمنتج '{$product->name}' أكبر من الكمية المتاحة في المخزون ({$product->stock}).",
                ]);
            }
        }
    }
    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id(); // ربط الفاتورة باليوزر
        // باقي الحسابات:
        $itemsTotal = 0;
        foreach ($data['items'] as $item) {
            $itemsTotal += (float) $item['quantity'] * (float) $item['price'];
        }

        $totalAfterDiscount = $itemsTotal - (float) $data['discount'];
        $data['total'] = $totalAfterDiscount + (float) $data['tax'];

        return $data;
    }
    public function afterCreate(Model $record): void
    {
        foreach ($record->items as $item) {
            if ($item->product_id) {
                $product = \App\Models\Product::find($item->product_id);
                $product->stock -= $item->quantity;
                $product->save();
            }
        }
    }
}
