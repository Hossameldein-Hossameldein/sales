<?php

namespace App\Filament\Resources\SalesInvoiceResource\Pages;

use App\Filament\Pages\CreateSalesInvoice;
use App\Filament\Resources\SalesInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSalesInvoices extends ListRecords
{
    protected static string $resource = SalesInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('اضافة فاتورة بيع')
                ->icon('heroicon-s-plus')
                ->url(CreateSalesInvoice::getUrl()),
        ];
    }
}
