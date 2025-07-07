<?php

namespace App\Filament\Resources\PurchaseInvoiceResource\Pages;

use App\Filament\Pages\CreatePurchaseInvoice;
use App\Filament\Resources\PurchaseInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPurchaseInvoices extends ListRecords
{
    protected static string $resource = PurchaseInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('اضافة فاتورة شراء')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->url(CreatePurchaseInvoice::getUrl()),

        ];
    }
}
