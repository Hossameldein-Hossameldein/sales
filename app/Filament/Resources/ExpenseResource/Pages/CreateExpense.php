<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExpense extends CreateRecord
{
    protected static string $resource = ExpenseResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id(); // ربط المصروف بالموظف اللي عمله
        return $data;
    }
}
