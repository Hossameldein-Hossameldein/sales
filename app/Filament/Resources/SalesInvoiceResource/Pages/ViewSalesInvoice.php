<?php

namespace App\Filament\Resources\SalesInvoiceResource\Pages;

use App\Filament\Resources\SalesInvoiceResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\{Grid, Section, TextEntry, RepeatableEntry};
use Filament\Infolists\Infolist;

class ViewSalesInvoice extends ViewRecord
{
    protected static string $resource = SalesInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('طباعة')
                ->icon('heroicon-o-printer')
                ->url(fn() => route('sales.print', ['record' => $this->record->id]))
                ->openUrlInNewTab(),
        ];
    }
}
