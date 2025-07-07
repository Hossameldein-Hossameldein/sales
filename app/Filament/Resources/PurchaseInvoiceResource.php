<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PurchaseInvoiceResource\Pages;
use App\Filament\Resources\PurchaseInvoiceResource\Pages\CreatePurchaseInvoice;
use App\Filament\Resources\PurchaseInvoiceResource\RelationManagers;
use App\Models\Category;
use App\Models\PurchaseInvoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;

class PurchaseInvoiceResource extends Resource
{
    protected static ?string $model = PurchaseInvoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getModelLabel(): string
    {
        return 'فاتورة شراء';
    }

    public static function getPluralLabel(): string
    {
        return 'فواتير الشراء';
    }

    public static function getNavigationLabel(): string
    {
        return 'فواتير الشراء';
    }


    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('supplier_id')
                ->label('المورد')
                ->options(\App\Models\Supplier::pluck('name', 'id'))
                ->searchable()
                ->required(),


            Select::make('payment_type')->label('نوع الدفع')->options([
                'كاش' => 'كاش',
                'آجل' => 'آجل',
            ])->default('كاش'),

            TextInput::make('discount')
                ->label('الخصم')
                ->numeric()
                ->default(0)
                ->reactive()
                ->afterStateUpdated(fn($state, callable $set, callable $get) => self::recalculateTotal($set, $get)),
            TextInput::make('tax')
                ->label('الضريبة')
                ->numeric()
                ->default(0)
                ->reactive()
                ->afterStateUpdated(fn($state, callable $set, callable $get) => self::recalculateTotal($set, $get)),

            TextInput::make('total')->label('الإجمالي')->numeric()->default(0)->disabled(),

            TextInput::make('notes')->label('ملاحظات'),

            Repeater::make('items')
                ->label('المنتجات')
                ->schema([
                    TextInput::make('product_barcode')
                        ->label('الباركود')
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set) {
                            $product = \App\Models\Product::where('barcode', $state)->first();

                            if ($product) {
                                $set('product_name', $product->name);
                                $set('purchase_price', $product->purchase_price);
                                $set('retail_price', $product->retail_price);
                                $set('wholesale_price', $product->wholesale_price);
                            } else {
                                // المنتج مش موجود → نسيب الباقي فاضي عشان المستخدم يدخله يدوي
                                $set('product_name', '');
                                $set('purchase_price', 0);
                                $set('retail_price', 0);
                                $set('wholesale_price', 0);
                            }
                        }),

                    TextInput::make('product_name')
                        ->label('اسم المنتج')
                        ->required(),

                    Select::make('category_id')
                        ->label('التصنيف')
                        ->options(Category::all()->pluck('name', 'id'))
                        ->preload()
                        ->required(),

                    TextInput::make('quantity')
                        ->label('الكمية')
                        ->numeric()
                        ->default(1)
                        ->live()
                        ->required(),

                    TextInput::make('purchase_price')
                        ->label('سعر الشراء')
                        ->numeric()
                        ->default(0)
                        ->live()
                        ->required(),

                    TextInput::make('retail_price')->label('سعر البيع القطاعي')
                        ->required()
                        ->numeric()
                        ->minValue(function ($get) {
                            return (float)$get('purchase_price');
                        })
                        ->default(0),

                    TextInput::make('wholesale_price')
                        ->label('سعر البيع الجملة')
                        ->required()
                        ->minValue(function ($get) {
                            return (float)$get('purchase_price');
                        })
                        ->numeric()->default(0),

                    

                ])
                ->columnSpanFull()
                ->reactive()
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    $itemsTotal = collect($state)->sum(
                        fn($item) =>
                        (float) ($item['purchase_price'] ?? 0) * (float) ($item['quantity'] ?? 0)
                    );

                    $discount = (float) $get('discount');
                    $tax = (float) $get('tax');
                    $total = $itemsTotal + $tax - $discount;

                    $set('total', $total);
                })
                ->columns(3)
                ->defaultItems(1)
                ->createItemButtonLabel('إضافة منتج'),

        ]);
    }

    private static function recalculateTotal(callable $set, callable $get)
    {
        $items = $get('items') ?? [];
        $itemsTotal = collect($items)->sum(
            fn($item) =>
            (float) ($item['purchase_price'] ?? 0) * (float) ($item['quantity'] ?? 0)
        );

        $discount = (float) $get('discount');
        $tax = (float) $get('tax');
        $total = $itemsTotal + $tax - $discount;

        $set('total', $total);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('رقم الفاتورة')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('supplier.name')
                    ->label('اسم المورد')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('date')
                    ->label('تاريخ الفاتورة')
                    ->date()
                    ->sortable(),

                TextColumn::make('payment_type')
                    ->badge()
                    ->label('نوع الدفع')
                    ->colors([
                        'success' => 'كاش',
                        'warning' => 'آجل',
                    ]),

                TextColumn::make('discount')
                    ->label('الخصم')
                    ->money('EGP')
                    ->sortable(),

                TextColumn::make('tax')
                    ->label('الضريبة')
                    ->money('EGP')
                    ->sortable(),

                TextColumn::make('total')
                    ->label('الإجمالي')
                    ->money('EGP')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('invoice_number')->label('رقم الفاتورة'),
                TextEntry::make('supplier.name')->label('المورد'),
                TextEntry::make('payment_type')->label('نوع الدفع'),
                TextEntry::make('discount')->label('الخصم'),
                TextEntry::make('tax')->label('الضريبة'),
                TextEntry::make('total')->label('الإجمالي'),
                TextEntry::make('notes')->label('ملاحظات'),

                // جدول المنتجات داخل العرض
                Section::make('المنتجات')
                    ->schema([
                        RepeatableEntry::make('items')
                            ->label('')
                            ->schema([
                                TextEntry::make('product_barcode')->label('الباركود'),
                                TextEntry::make('product_name')->label('اسم المنتج'),
                                TextEntry::make('quantity')->label('الكمية'),
                                TextEntry::make('purchase_price')->label('سعر الشراء'),
                                TextEntry::make('total')->label('الإجمالي'),
                            ])
                            ->columns(5)
                            ->columnSpanFull()
                    ])
                    ->collapsed(), // افتراضي تكون مطوية، تقدر تشيلها لو عايزها مفتوحة

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
            'index' => Pages\ListPurchaseInvoices::route('/'),
            'edit' => Pages\EditPurchaseInvoice::route('/{record}/edit'),
            'view' => Pages\ViewPurchaseInvoice::route('/{record}'),
        ];
    }
}
