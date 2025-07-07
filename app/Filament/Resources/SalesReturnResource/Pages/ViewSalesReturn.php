<?php

namespace App\Filament\Resources\SalesReturnResource\Pages;

use App\Filament\Resources\SalesReturnResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\{Grid, TextEntry, RepeatableEntry};

class ViewSalesReturn extends ViewRecord
{
    protected static string $resource = SalesReturnResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Grid::make(2)
                    ->schema([
                        TextEntry::make('id')->label('رقم المرتجع'),
                        TextEntry::make('invoice.invoice_number')->label('رقم فاتورة البيع'),
                        TextEntry::make('user.name')->label('تم بواسطة'),
                        TextEntry::make('created_at')
                            ->label('تاريخ الإنشاء')
                            ->dateTime('d/m/Y H:i'),
                        TextEntry::make('total')
                            ->label('الإجمالي')
                            ->money('EGP'),
                        TextEntry::make('notes')->label('السبب')->columnSpanFull(),
                    ]),

                RepeatableEntry::make('items')
                    ->label('المنتجات المرتجعة')
                    ->columnSpanFull()
                    ->columns(5)
                    ->schema([
                        TextEntry::make('product_name')->label('المنتج'),
                        TextEntry::make('barcode')->label('الباركود'),
                        TextEntry::make('quantity')->label('الكمية'),
                        TextEntry::make('price')->label('السعر')->money('EGP'),
                        TextEntry::make('total')->label('الإجمالي')->money('EGP'),
                    ]),
            ]);
    }
}
