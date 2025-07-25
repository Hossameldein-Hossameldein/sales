<?php

namespace App\Filament\Resources\InventoryReportResource\Pages;

use App\Filament\Resources\InventoryReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInventoryReport extends ViewRecord
{
    protected static string $resource = InventoryReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
