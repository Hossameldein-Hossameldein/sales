<?php

namespace App\Filament\Pages;

use App\Filament\Resources\PurchaseInvoiceResource;
use Filament\Pages\Page;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseItem;
use Filament\Forms\Form;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\{TextInput, Select, Grid, Repeater, Section, Hidden};

class CreatePurchaseInvoice extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';
    protected static ?string $navigationLabel = 'إنشاء فاتورة شراء';
    protected static string $view = 'filament.pages.create-purchase-invoice';

    protected static ?string $title = 'إنشاء فاتورة شراء';

    public ?array $formData = [];

    public function mount(): void
    {
        $this->form->fill([]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('formData')
            ->schema([
                Section::make('بيانات الفاتورة')->schema([
                    Grid::make(3)->schema([
                        Select::make('supplier_id')
                            ->label('المورد')
                            ->options(Supplier::pluck('name', 'id'))
                            ->required()
                            ->searchable(),

                        Select::make('payment_method')
                            ->label('طريقة الدفع')
                            ->options([
                                'كاش' => 'كاش',
                                'اجل' => 'اجل',
                            ])
                            ->default('كاش')
                            ->formatStateUsing(fn($state) => $state ?? 'كاش'),
                        TextInput::make('discount')
                            ->label('الخصم')
                            ->numeric()
                            ->default("0")
                            ->formatStateUsing(fn($state) => $state ?? 0),

                        TextInput::make('tax')
                            ->label('الضريبة')
                            ->numeric()
                            ->default("0")
                            ->formatStateUsing(fn($state) => $state ?? 0),

                        TextInput::make('total')
                            ->label('الإجمالي')
                            ->numeric()
                            ->default("0")
                            ->formatStateUsing(fn($state) => $state ?? 0)
                            ->disabled()
                            ->dehydrated(),

                        TextInput::make('notes')
                            ->label('ملاحظات')
                            ->maxLength(255),

                    ]),
                ]),

                Section::make('المنتجات')->schema([
                    Repeater::make('items')
                        ->label('')
                        ->defaultItems(1)
                        ->createItemButtonLabel('إضافة منتج')
                        ->schema([
                            Grid::make(3)->schema([
                                TextInput::make('product_barcode')
                                    ->label('الباركود')
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $product = Product::where('barcode', $state)->first();
                                        if ($product) {
                                            $set('product_name', $product->name);
                                            $set('purchase_price', $product->purchase_price);
                                            $set('retail_price', $product->retail_price);
                                            $set('wholesale_price', $product->wholesale_price);
                                        }
                                    }),

                                TextInput::make('product_name')
                                    ->label('اسم المنتج')
                                    ->required(),

                                Select::make('category_id')
                                    ->label('الصنف')
                                    ->options(\App\Models\Category::pluck('name', 'id'))
                                    ->required(),

                                TextInput::make('quantity')
                                    ->label('الكمية')
                                    ->numeric()
                                    ->required()
                                    ->default(1),

                                TextInput::make('purchase_price')
                                    ->label('سعر الشراء')
                                    ->numeric()
                                    ->required()
                                    ->default(0),

                                TextInput::make('retail_price')
                                    ->label('سعر البيع القطاعي')
                                    ->numeric()
                                    ->required()
                                    ->default(0),

                                TextInput::make('wholesale_price')
                                    ->label('سعر الجملة')
                                    ->numeric()
                                    ->required()
                                    ->default(0),
                            ]),
                        ])
                        ->afterStateUpdated(function (callable $set, callable $get) {
                            $total = 0;
                            foreach ($get('items') as $item) {
                                $total += (float) ($item['quantity'] ?? 0) * (float) ($item['purchase_price'] ?? 0);
                            }

                            $total = $total - (float) ($get('discount') ?? 0) + (float) ($get('tax') ?? 0);
                            $set('total', $total);
                        }),
                ]),
            ]);
    }

    public function create(): void
    {
        $data = $this->form->getState();

        $total = 0;
        foreach ($data['items'] as $item) {
            $name = $item['product_name'];
            if ($item['retail_price'] < $item['purchase_price']) {
                Notification::make()
                    ->title("خطأ في سعر البيع القطاعي")
                    ->body("سعر البيع القطاعي للمنتج ($name) لازم يكون أكبر من سعر الشراء.")
                    ->danger()
                    ->send();
                return;
            }

            if ($item['wholesale_price'] < $item['purchase_price']) {
                Notification::make()
                    ->title("خطأ في سعر البيع الجملة")
                    ->body("سعر البيع الجملة للمنتج ($name) لازم يكون أكبر من سعر الشراء.")
                    ->danger()
                    ->send();
                return;
            }
            $total += $item['quantity'] * $item['purchase_price'];
        }

        $data['total'] = $total - $data['discount'] + $data['tax'];


        $invoice = PurchaseInvoice::create(collect($data)->except('items')->toArray());

        foreach ($data['items'] as $item) {
            $product = Product::firstOrCreate(
                ['barcode' => $item['product_barcode']],
                [
                    'name' => $item['product_name'],
                    'purchase_price' => $item['purchase_price'],
                    'retail_price' => $item['retail_price'],
                    'wholesale_price' => $item['wholesale_price'],
                    'stock' => 0,
                    'category_id' => $item['category_id'],
                ]
            );

            $product->increment('stock', $item['quantity']);

            PurchaseItem::create([
                'purchase_invoice_id' => $invoice->id,
                'product_id' => $product->id,
                'product_name' => $item['product_name'],
                'product_barcode' => $item['product_barcode'],
                'quantity' => $item['quantity'],
                'purchase_price' => $item['purchase_price'],
                'retail_price' => $item['retail_price'],
                'wholesale_price' => $item['wholesale_price'],
                'total' => $item['quantity'] * $item['purchase_price'],
            ]);
        }

        Notification::make()
            ->title('تم حفظ الفاتورة بنجاح')
            ->success()
            ->send();

        $this->redirect(PurchaseInvoiceResource::getUrl('view', ['record' => $invoice]));
    }
}
