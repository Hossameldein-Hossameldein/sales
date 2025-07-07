<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Product;
use App\Models\PurchaseItem;
use App\Models\SalesItem;
use App\Models\SalesReturnItem;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;

class ItemCardDetail extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'كارت الصنف التفصيلي';
    protected static string $view = 'filament.pages.item-card-detail';

    

    public ?int $productId = null;
    public $purchases = [];
    public $sales = [];

    public $returns = [];

    public $analytics = [
        'purchases' => 0,
        'sales' => 0,
        'returns' => 0,
        'stock' => 0,
        'profit' => 0
    ];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('productId')
                ->label('اختار الصنف')
                ->options(Product::pluck('name', 'barcode'))
                ->searchable()
                // ->getSearchResultsUsing(
                //     fn(string $query) => Product::where('name', 'like', "%{$query}%")
                //         ->orWhere('barcode', 'like', "%{$query}%")
                //         ->pluck('name', 'barcode')->toArray()
                // )
                ->searchable()
                ->reactive()
                ->afterStateUpdated(fn($state) => $this->loadData($state))
        ]);
    }



    public function loadData($productId): void
    {
        $productId = Product::where('barcode', $productId)->first()->id;

        $this->productId = $productId;

        $this->purchases = PurchaseItem::with('invoice')
            ->where('product_id', $productId)
            ->get();

        $this->sales = SalesItem::with('invoice')
            ->where('product_id', $productId)
            ->get();

        $this->returns = SalesReturnItem::with('return')
            ->where('product_id', $productId)
            ->get();

        $this->analytics = [
            'purchases' => (int) $this->purchases->sum('quantity'),
            'sales' => (int) $this->sales->sum('quantity'),
            'returns' =>(int) $this->returns->sum('quantity'),
            'stock' => (int) Product::find($productId)->stock
        ];
    }

    protected function getFormModel(): string
    {
        return Product::class;
    }
}
