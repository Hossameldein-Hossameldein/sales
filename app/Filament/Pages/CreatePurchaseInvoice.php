<?php

namespace App\Filament\Pages;

use App\Filament\Resources\PurchaseInvoiceResource;
use App\Models\Category;
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
use Filament\Forms\Components\{TextInput, Select, Grid, Repeater, Section, Hidden, View};
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Get;
use Filament\Forms\Set;

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
                    Select::make('selected_product')
                        ->hintActions([
                            Action::make('add')
                                ->label('اضافة')
                                ->icon('heroicon-s-plus')
                                ->color('success')
                                ->form([
                                    Section::make('بيانات المنتج')->schema([
                                        TextInput::make('name')->label('اسم المنتج')->required(),
                                        TextInput::make('barcode')->label('باركود المنتج')->required(),
                                        Select::make('category_id')->label('القسم')->options(Category::pluck('name', 'id'))->required(),
                                        TextInput::make('purchase_price')
                                            ->numeric()
                                            ->label('سعر الشراء')->required(),
                                        TextInput::make('retail_price')
                                            ->numeric()
                                            ->label('سعر البيع قطاعي')->required(),
                                        TextInput::make('wholesale_price')
                                            ->numeric()
                                            ->label('سعر الجملة')->required(),
                                    ])->columns(2)


                                ])
                                ->action(function (array $data, Get $get, Set $set) {
                                    $product = Product::where('barcode', $data['barcode'])->first();

                                    if ($product) {
                                        Notification::make()->title('المنتج مضاف بالفعل')->warning()->send();
                                        return;
                                    }

                                    $product = Product::create([
                                        'name' => $data['name'],
                                        'barcode' => $data['barcode'],
                                        'category_id' => $data['category_id'],
                                        'stock' => 0,
                                        'purchase_price' => $data['purchase_price'],
                                        'retail_price' => $data['retail_price'],
                                        'wholesale_price' => $data['wholesale_price'],

                                    ]);

                                    $selected_products = $get('selected_products') ?? [];

                                    $selected_products[] = [
                                        'id' => $product->id,
                                        'product_barcode' => $product->barcode,
                                        'product_name' => $product->name,
                                        'category_id' => $product->category_id,
                                        'quantity' => 1,
                                        'purchase_price' =>(int) $product->purchase_price,
                                        'retail_price' => (int)$product->retail_price,
                                        'wholesale_price' =>(int) $product->wholesale_price,
                                        'total' =>(int) $product->purchase_price,
                                        'barcode' => $product->barcode
                                    ];

                                    $set('selected_products', $selected_products);

                                    Notification::make()->title('تمت الاضافة بنجاح')->success()->send();
                                })
                        ])
                        ->label('اختر منتج')
                        ->preload()
                        ->options(Product::pluck('name', 'id'))
                        ->searchable()
                        ->getSearchResultsUsing(function (string $search , Get $get , Set $set) {
                            if (is_numeric($search)) {
                                return Product::where('barcode', $search)->pluck('name', 'id')->toArray();
                            }

                            return Product::where('name', 'like', "%{$search}%")->pluck('name', 'id')->toArray();
                        })
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $get, callable $set) {
                            if (!$state) return;

                            $existing = $get('selected_products') ?? [];
                            $product = Product::find($state);

                            if (!$product) return;

                            // لو مكرر نمنع الإضافة
                            foreach ($existing as $item) {
                                if ($item['id'] == $product->id) {
                                    Notification::make()->title('المنتج مضاف بالفعل')->warning()->send();
                                    return;
                                }
                            }

                            $existing[] = [
                                'id' => $product->id,
                                'product_barcode' => $product->barcode,
                                'product_name' => $product->name,
                                'category_id' => $product->category_id,
                                'quantity' => 1,
                                'purchase_price' =>(int) $product->purchase_price,
                                'retail_price' => (int)$product->retail_price,
                                'wholesale_price' => (int)$product->wholesale_price,
                                'total' =>(int) $product->purchase_price,
                                'barcode' => $product->barcode
                            ];

                            $set('selected_products', $existing);
                            $set('selected_product', null);
                        }),
                ]),
                View::make('components.selected-purchase-products-table')
                    ->label('')
                    ->reactive()
                    ->dehydrated()
                    ->live(),

            ]);
    }
    public function removeProduct($index)
    {
        $products = $this->formData['selected_products'] ?? [];
        unset($products[$index]);
        $this->formData['selected_products'] = array_values($products);
    }

    public function updateTotal($index)
    {
        $item = $this->formData['selected_products'][$index];

        $quantity = (float) ($item['quantity'] ?? 1);
        $price = (float) ($item['purchase_price'] ?? 0);

        $this->formData['selected_products'][$index]['total'] = $quantity * $price;

        // تحديث الإجمالي الكلي
        $this->formData['total'] = collect($this->formData['selected_products'])->sum(function ($item) {
            return (float) $item['quantity'] * (float) $item['purchase_price'];
        }) - (float) ($this->formData['discount'] ?? 0) + (float) ($this->formData['tax'] ?? 0);
    }


    public function create(): void
    {
        $data = $this->form->getState();

        $items = $this->formData['selected_products'] ?? [];

        if (empty($items)) {
            Notification::make()
                ->title('لا يوجد منتجات')
                ->body('يجب إضافة منتج واحد على الأقل')
                ->danger()
                ->send();
            return;
        }

        $total = 0;
        foreach ($items as $item) {
            $name = $item['product_name'];

            if ($item['quantity'] <= 0) {
                Notification::make()
                    ->title("خطاء في كمية المنتج ($name)")
                    ->body("يجب تحديد كمية المنتج.")
                    ->danger()
                    ->send();
                return;
            }

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

        // حفظ الفاتورة
        $invoice = PurchaseInvoice::create(collect($data)->except('selected_products', 'selected_product')->toArray());

        foreach ($items as $item) {
            $product = Product::where('barcode', $item['product_barcode'])->first();

            if ($product) {
                $product->update([
                    'name' => $item['product_name'],
                    'purchase_price' => $item['purchase_price'],
                    'retail_price' => $item['retail_price'],
                    'wholesale_price' => $item['wholesale_price'],
                    'category_id' => $item['category_id'],
                ]);
            } else {
                $product = Product::create([
                    'barcode' => $item['product_barcode'],
                    'name' => $item['product_name'],
                    'purchase_price' => $item['purchase_price'],
                    'retail_price' => $item['retail_price'],
                    'wholesale_price' => $item['wholesale_price'],
                    'stock' => 0,
                    'category_id' => $item['category_id'],
                ]);
            }



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
