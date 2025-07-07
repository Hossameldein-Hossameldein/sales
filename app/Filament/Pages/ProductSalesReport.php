<?php

namespace App\Filament\Pages;

use App\Models\Product;
use App\Models\SalesItem;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class ProductSalesReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $title = 'تقرير مبيعات المنتجات';
    protected static ?string $navigationLabel = 'مبيعات المنتجات';
    protected static string $view = 'filament.pages.product-sales-report';

    public ?string $start_date = null;
    public ?string $end_date = null;

    public $topProducts = [];

    public function mount()
    {
        $this->generateReport();
    }

    public function updated($property)
    {
        $this->generateReport();
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                DatePicker::make('start_date')
                    ->label('من تاريخ')
                    ->default(Carbon::now()->startOfMonth())
                    ->reactive(),

                DatePicker::make('end_date')
                    ->label('إلى تاريخ')
                    ->default(Carbon::now())
                    ->reactive(),
            ])
        ]);
    }

    public function generateReport()
    {
        $query = SalesItem::query()
            ->selectRaw('product_id, product_name, SUM(quantity) as total_quantity, SUM(total) as total_sales')
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('total_quantity');

        if ($this->start_date) {
            $query->whereDate('created_at', '>=', $this->start_date);
        }

        if ($this->end_date) {
            $query->whereDate('created_at', '<=', $this->end_date);
        }

        $this->topProducts = $query->take(10)->get();
    }
}
