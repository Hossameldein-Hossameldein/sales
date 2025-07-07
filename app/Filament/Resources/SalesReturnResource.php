<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesReturnResource\Pages;
use App\Filament\Resources\SalesReturnResource\RelationManagers;
use App\Filament\Resources\SalesReturnResource\RelationManagers\ItemsRelationManager;
use App\Models\SalesReturn;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use App\Models\SalesInvoice;
use App\Models\Product;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;

class SalesReturnResource extends Resource
{
    protected static ?string $model = SalesReturn::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return 'فاتورة مرتجع';
    }

    public static function getPluralLabel(): string
    {
        return 'المرتجعات';
    }

    public static function getNavigationLabel(): string
    {
        return 'المرتجعات';
    }


    public static function form(Form $form): Form
    {
        return $form->schema([

            Select::make('sales_invoice_id')
                ->label('فاتورة البيع')
                ->options(SalesInvoice::all()->pluck('invoice_number', 'id'))
                ->searchable()
                ->required()
                ->reactive()
                ->afterStateUpdated(fn($state, callable $set) => $set('items', [])),

            TextInput::make('notes')->label('سبب المرتجع')->nullable(),

            Repeater::make('items')
                ->label('المنتجات المرتجعة')
                ->columnSpanFull()
                ->schema([
                    Select::make('product_id')
                        ->label('المنتج')
                        ->options(
                            fn(callable $get) =>
                            SalesInvoice::find($get('../../sales_invoice_id'))?->items
                                ->pluck('product.name', 'product_id') ?? []
                        )
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $product = Product::find($state);
                            if ($product) {
                                $set('product_name', $product->name);
                                $set('barcode', $product->barcode);
                                $set('price', $product->retail_price);
                                $set('total', $product->retail_price);
                            }
                        }),

                    TextInput::make('product_name')->label('اسم المنتج')->disabled(),
                    TextInput::make('barcode')->label('الباركود')->disabled(),

                    TextInput::make('quantity')->label('الكمية المرتجعة')->numeric()->required()
                        ->default(1)
                        ->maxValue(function (callable $get) {
                            return SalesInvoice::find($get('../../sales_invoice_id'))?->items
                                ->where('product_id', $get('product_id'))
                                ->sum('quantity');
                        })
                        ->reactive()
                        ->afterStateUpdated(
                            fn($state, callable $set, callable $get) =>
                            $set('total', (float) $state * (float) $get('price'))
                        ),

                    TextInput::make('price')->label('سعر البيع')
                        ->numeric()
                        ->required()
                        ->default(0)
                        ->reactive()
                        ->afterStateUpdated(
                            fn($state, callable $set, callable $get) =>
                            $set('total', (float) $state * (float) $get('quantity'))
                        ),

                    TextInput::make('total')->label('الإجمالي')->numeric()->disabled()->dehydrated(false),
                ])
                ->defaultItems(0)
                ->columns(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('رقم المرتجع')->sortable(),
                TextColumn::make('invoice.invoice_number')->label('رقم الفاتورة الأصلية'),
                TextColumn::make('user.name')->label('تم بواسطة'),
                TextColumn::make('total')->label('الإجمالي')->money('EGP'),
                TextColumn::make('created_at')->label('التاريخ')->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                Filter::make('created_today')
                    ->label('اليوم فقط')
                    ->query(fn($query) => $query->whereDate('created_at', now())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // ItemsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalesReturns::route('/'),
            'create' => Pages\CreateSalesReturn::route('/create'),
            'view' => Pages\ViewSalesReturn::route('/{record}'),
            // 'edit' => Pages\EditSalesReturn::route('/{record}/edit'),
        ];
    }
}
