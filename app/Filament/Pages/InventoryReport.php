<?php

namespace App\Filament\Pages;

use App\Models\Product;
use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class InventoryReport extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'جرد المخزون';
    protected static ?string $title = 'جرد المخزون';
    protected static string $view = 'filament.pages.inventory-report';

    public function table(Table $table): Table
    {
        return $table
            ->query(Product::query())
            ->columns([
                TextColumn::make('name')->label('اسم المنتج')->searchable(),
                TextColumn::make('barcode')->label('الباركود')->searchable(),
                TextColumn::make('category.name')->label('الصنف'),
                TextColumn::make('unit')->label('الوحدة'),
                TextColumn::make('stock')->label('الكمية')->sortable(),
                TextColumn::make('purchase_price')->label('سعر الشراء')->money('EGP'),
                TextColumn::make('retail_price')->label('سعر القطاعي')->money('EGP'),
                TextColumn::make('wholesale_price')->label('سعر الجملة')->money('EGP'),
                TextColumn::make('قيمة المخزون')
                    ->getStateUsing(fn($record) => $record->stock * $record->purchase_price)
                    ->label('قيمة المخزون')
                    ->money('EGP'),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('الصنف')
                    ->relationship('category', 'name'),
            ])
            ->headerActions([]);
    }
}
