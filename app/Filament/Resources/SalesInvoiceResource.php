<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SalesInvoiceResource\Pages;
use App\Filament\Resources\SalesInvoiceResource\RelationManagers;
use App\Models\SalesInvoice;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Tables\Filters\Filter;

class SalesInvoiceResource extends Resource
{
    protected static ?string $model = SalesInvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return 'فاتورة بيع';
    }

    public static function getPluralLabel(): string
    {
        return 'فواتير البيع';
    }

    public static function getNavigationLabel(): string
    {
        return 'فواتير البيع';
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('invoice_number')->label('رقم الفاتورة')->required(),

                Select::make('customer_id')
                    ->label('العميل')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->nullable(),

                Select::make('sale_type')->label('نوع البيع')->options([
                    'قطاعي' => 'قطاعي',
                    'جملة' => 'جملة',
                ])->default('قطاعي')->required()->reactive(),

                TextInput::make('discount')->label('الخصم')->numeric()->default(0),
                TextInput::make('tax')->label('الضريبة')->numeric()->default(0),
                TextInput::make('total')->label('الإجمالي')->numeric()->disabled()->dehydrated(false),

                TextInput::make('notes')->label('ملاحظات'),

                Repeater::make('items')
                    ->label('المنتجات')
                    ->relationship()
                    ->schema([
                        Select::make('product_id')
                            ->label('المنتج')
                            ->relationship('product', 'name')
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $product = \App\Models\Product::find($state);
                                if (!$product) return;

                                $saleType = $get('../../sale_type') ?? 'قطاعي';
                                $set('product_name', $product->name);
                                $set('barcode', $product->barcode);
                                $set('price', $saleType === 'جملة' ? $product->wholesale_price : $product->retail_price);
                            }),

                        TextInput::make('product_name')->label('اسم المنتج')->required(),
                        TextInput::make('barcode')->label('الباركود')->disabled(),
                        TextInput::make('quantity')->label('الكمية')->numeric()->default(1)->reactive()
                            ->afterStateUpdated(
                                fn($state, callable $set, callable $get) =>
                                $set('total', (float) $state * (float) $get('price'))
                            ),

                        TextInput::make('price')
                        ->label('السعر')->numeric()->default(0)->reactive()
                            ->afterStateUpdated(
                                fn($state, callable $set, callable $get) =>
                                $set('total', (float) $state * (float) $get('quantity'))
                            ),

                        TextInput::make('total')->label('الإجمالي')->numeric()->disabled()->dehydrated(false),
                    ])
                    ->columns(4)
                    ->defaultItems(1)
                    ->createItemButtonLabel('إضافة منتج'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')->label('رقم الفاتورة')->searchable(),
                Tables\Columns\TextColumn::make('customer.name')->label('العميل'),
                Tables\Columns\TextColumn::make('sale_type')->label('النوع'),
                Tables\Columns\TextColumn::make('total')->label('الإجمالي')->money('EGP'),
                Tables\Columns\TextColumn::make('created_at')->label('التاريخ')->dateTime()->sortable()
            ])
            ->filters([
                Filter::make('created_at')
                    ->label('التاريخ')
                    ->form([
                        DatePicker::make('from')->label('من'),
                        DatePicker::make('to')->label('إلى'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderByDesc('created_at')
            ->when(\auth()->user()->hasRole('admin'), function ($query) {
                return $query;
            })
        ;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Grid::make(2)->schema([
                    Section::make('بيانات الفاتورة')->schema([
                        TextEntry::make('invoice_number')->label('رقم الفاتورة'),
                        TextEntry::make('date')->label('التاريخ')->date(),
                        TextEntry::make('sale_type')->label('نوع البيع'),
                        TextEntry::make('customer.name')->label('العميل')->default('---'),
                        TextEntry::make('user.name')->label('تم الإنشاء بواسطة')->default('---'),
                        TextEntry::make('notes')->label('ملاحظات')->default('---'),
                    ])
                        ->columns(3),
                    Section::make('الإجماليات')->schema([
                        TextEntry::make('discount')->label('الخصم')->money('EGP'),
                        TextEntry::make('tax')->label('الضريبة')->money('EGP'),
                        TextEntry::make('total')->label('الإجمالي')->money('EGP'),
                    ])
                        ->columns(3),
                ]),

                Section::make('تفاصيل المنتجات')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                TextEntry::make('product_name')->label('اسم المنتج'),
                                TextEntry::make('barcode')->label('الباركود'),
                                TextEntry::make('price')->label('السعر')->money('EGP'),
                                TextEntry::make('quantity')->label('الكمية'),
                                TextEntry::make('total')->label('الإجمالي')->money('EGP'),
                            ])
                            ->columns(5)
                            ->columnSpanFull(),
                    ])
                    ->collapsed(false),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSalesInvoices::route('/'),
            // 'edit' => Pages\EditSalesInvoice::route('/{record}/edit'),
            'view' => Pages\ViewSalesInvoice::route('/{record}'),
        ];
    }
}
