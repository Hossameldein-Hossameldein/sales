<?php

namespace App\Filament\Pages;

use App\Filament\Resources\SalesInvoiceResource;
use Filament\Pages\Page;
use App\Models\{Customer, Product, SalesInvoice, SalesItem};
use Illuminate\Support\Str;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\{Select, TextInput, Textarea, Grid, Section, Repeater};
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class CreateSalesInvoice extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static string $view = 'filament.pages.create-sales-invoice';
    protected static ?string $title = 'إنشاء فاتورة بيع';

    public ?array $formData = [];

    public array $selectedProducts = [];

    public array $productsState = [];

    protected $listeners = ['productSelectedFromSearch'];


    public function mount(): void
    {
        $this->form->fill([
            'invoice_number' => 'INV-' . now()->format('Ymd-His'),
        ]);
    }

    public function productSelectedFromSearch($productId)
    {
        $product = Product::find($productId);
        if (!$product) return;

        $existing = $this->productsState ?? [];

        foreach ($existing as $i => $item) {
            if ($item['id'] == $productId) {
                $this->productsState[$i]['quantity']++;
                Notification::make()->title('مضاف بالفعل وتم تزويد العدد واحد')->warning()->send();
                return;
            }
        }

        $existing[] = [
            'id' => $product->id,
            'name' => $product->name,
            'barcode' => $product->barcode,
            'price' => $this->formData['sale_type'] == 'قطاعي'
                ? $product->retail_price
                : $product->wholesale_price,
            'quantity' => 1,
            'stock' => $product->stock,
            'total' => $this->formData['sale_type'] == 'قطاعي'
                ? $product->retail_price
                : $product->wholesale_price,
        ];

        $this->productsState = $existing;
        $this->selectedProducts = $existing;

        $total = 0;
        foreach ($existing as $item) {
            $total += $item['total'] * $item['quantity'];
        }
        $this->formData['total'] = $total - ($this->formData['discount'] ?? 0);
    }
    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make('بيانات الفاتورة')->schema([

                Select::make('customer_id')
                    ->label('العميل')
                    ->options(Customer::pluck('name', 'id'))
                    ->nullable()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->hintAction(
                        Action::make('add_customer')
                            ->icon('heroicon-o-plus')
                            ->label('اضافة')
                            ->form([
                                TextInput::make('name')->label('اسم العميل')->required(),
                                TextInput::make('phone')
                                    ->label('رقم الهاتف')
                                    ->tel()
                                    ->rules([
                                        'unique:customers,phone',
                                    ])
                                    ->required(),
                                TextInput::make('email')->label('البريد الالكتروني'),
                                TextInput::make('address')->label('العنوان'),
                            ])
                            ->action(function (array $data, callable $get, callable $set) {
                                Customer::create($data);

                                $set('customer_id', Customer::latest()->first()->id);
                                Notification::make()
                                    ->success()
                                    ->title('تم اضافة العميل بنجاح');
                            })

                    ),

                Select::make('sale_type')->label('نوع البيع')->options([
                    'قطاعي' => 'قطاعي',
                    'جملة' => 'جملة',
                ])
                    ->default('قطاعي')
                    ->formatStateUsing(fn($state) => $state ?? 'قطاعي')
                    ->required()->reactive(),

                TextInput::make('invoice_number')->label('رقم الفاتورة')->disabled()->dehydrated(true),
                TextInput::make('discount')->label('الخصم')
                    ->numeric()
                    ->default(0)
                    ->formatStateUsing(fn($state) => $state ?? 0)
                    ->afterStateUpdated(function ($state, callable $get, callable $set) {
                        $set('total', $get('total') - $state);
                    }),

                // TextInput::make('total')->label('الإجمالي')
                //     ->numeric()
                //     ->disabled()
                //     ->dehydrated(false),


            ])->columns(4),

            Section::make('المنتجات')->schema([
                // Select::make('selected_product')
                //     ->label('اختر منتج')
                //     ->options(Product::pluck('name', 'id'))
                //     ->searchable()
                //     ->reactive()
                //     ->getSearchResultsUsing(function (string $search) {
                //         if (is_numeric($search)) {
                //             return Product::where('barcode', $search)->pluck('name', 'id');
                //         }

                //         return Product::where('name', 'like', '%' . $search . '%')->pluck('name', 'id');
                //     })
                //     ->afterStateUpdated(function ($state, callable $get, callable $set) {
                //         if (!$state) return;

                //         $existing = $this->productsState ?? [];
                //         $product = Product::find($state);
                //         if (!$product) return;

                //         // منع التكرار
                //         foreach ($existing as $item) {
                //             if ($item['id'] == $state) {
                //                 Notification::make()
                //                     ->title('المنتج مضاف بالفعل')
                //                     ->warning()
                //                     ->send();
                //                 $set('selected_product', null);
                //                 return;
                //             }
                //         }

                //         $existing[] = [
                //             'id' => $product->id,
                //             'name' => $product->name,
                //             'barcode' => $product->barcode,
                //             'price' => $get('sale_type') == 'قطاعي' ? $product->retail_price : $product->wholesale_price,
                //             'quantity' => 1,
                //             'stock' => $product->stock,
                //             'total' => $product->retail_price,
                //         ];


                //         $set('selected_products', $existing);

                //         $this->selectedProducts = $existing;
                //         $total = 0;

                //         foreach ($existing as $item) {
                //             $total += $item['total'] * $item['quantity'];
                //         }

                //         $set('total', $total);

                //         $set('selected_product', null); // إعادة ضبط السلكت
                //     }),
                \Filament\Forms\Components\View::make('components.products-search')
                    ->live()
                    ->reactive()
                    ->label(''),

            ]),


            \Filament\Forms\Components\View::make('components.selected-products-table')
                ->live()
                ->reactive()
                ->label(''),



            Section::make('ملاحظات')->schema([
                Textarea::make('notes')->label('ملاحظات'),
            ]),
        ])

            ->statePath('formData');
    }

    public function removeProduct($index)
    {
        $products = $this->productsState ?? [];
        unset($products[$index]);
        $this->productsState = $products;
    }

    public function updatedFormDataSelectedProducts()
    {
        $total = 0;

        foreach ($this->productsState as $index => $item) {
            $qty = (float) $item['quantity'];
            $price = (float) $item['price'];
            $this->productsState[$index]['total'] = $qty * $price;

            $total += $this->productsState[$index]['total'];
        }
        $this->formData['total'] = $total - ($this->formData['discount'] ?? 0);

        $this->selectedProducts = $this->productsState;
    }

    public function updated($name, $value)
    {
        if (Str::startsWith($name, 'formData.selected_products')) {
            $total = 0;

            foreach ($this->productsState ?? [] as $i => &$item) {
                $item['total'] = (float) $item['price'] * (float) ($item['quantity'] ?? 1);
                $total += $item['total'];
            }

            $this->formData['total'] = $total - ($this->formData['discount'] ?? 0);

            $this->selectedProducts = $this->productsState;
        }
    }



    public function create(): void
    {
        try {
            $data = $this->form->getState();


            if (SalesInvoice::where('invoice_number', $data['invoice_number'])->exists()) {
                $data['invoice_number'] = 'INV-' . now()->format('Ymd-His');
            }

            $items = $this->productsState ?? [];

            // تحقق من صحة المنتجات
            foreach ($items as $index => $item) {
                if (($item['price'] ?? 0) < 0 || ($item['quantity'] ?? 0) <= 0) {
                    Notification::make()
                        ->title("خطأ في المنتج " . ($item['name'] ?? ''))
                        ->body("تأكد من أن السعر أكبر من 0 والكمية أكبر من 0")
                        ->danger()
                        ->send();
                    return;
                }

                if ($item['quantity'] > Product::find($item['id'])['stock']) {
                    Notification::make()
                        ->title("خطاء في المنتج " . ($item['name'] ?? ''))
                        ->body("تأكد من صحة كمية المنتج")
                        ->danger()
                        ->send();
                    return;
                }
            }

            // حساب الإجمالي
            $total = 0;
            foreach ($items as $index => $item) {
                $items[$index]['total'] = (float) $item['price'] * (float) $item['quantity'];
                $total += $items[$index]['total'];
            }

            $data['total'] = $total - ($data['discount'] ?? 0) + ($data['tax'] ?? 0);

            // إنشاء الفاتورة
            $invoice = SalesInvoice::create([
                'customer_id' => $data['customer_id'],
                'invoice_number' => $data['invoice_number'],
                'sale_type' => $data['sale_type'],
                'discount' => $data['discount'] ?? 0,
                'tax' => $data['tax'] ?? 0,
                'total' => $data['total'],
                'notes' => $data['notes'] ?? null,
                'user_id' => auth()->id(),
            ]);

            // حفظ العناصر
            foreach ($items as $item) {
                SalesItem::create([
                    'sales_invoice_id' => $invoice->id,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'barcode' => $item['barcode'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['total'],
                ]);

                Product::find($item['id'])?->decrement('stock', $item['quantity']);
            }

            Notification::make()
                ->title('تم إنشاء الفاتورة بنجاح')
                ->success()
                ->send();

            $this->redirect(SalesInvoiceResource::getUrl('view', ['record' => $invoice->id]));
        } catch (\Throwable $e) {
            Notification::make()
                ->title('حدث خطأ أثناء إنشاء الفاتورة')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
