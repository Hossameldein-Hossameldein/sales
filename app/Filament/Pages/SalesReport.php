<?php
namespace App\Filament\Pages;

use App\Models\SalesInvoice;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class SalesReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $title = 'تقرير المبيعات';
    protected static ?string $navigationLabel = 'تقرير المبيعات';
    protected static string $view = 'filament.pages.sales-report';

    public ?string $type = null;
    public ?string $user = null;
    public ?string $start_date = null;
    public ?string $end_date = null;

    public $data = [];

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
            Grid::make(4)->schema([
                Select::make('type')
                    ->label('نوع البيع')
                    ->options([
                        '' => 'الكل',
                        'قطاعي' => 'قطاعي',
                        'جملة' => 'جملة',
                    ])
                    ->reactive(),

                Select::make('user')
                    ->label('الموظف')
                    ->options(\App\Models\User::pluck('name', 'id'))
                    ->searchable()
                    ->reactive(),

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
        $query = SalesInvoice::query();

        if ($this->type) {
            $query->where('sale_type', $this->type);
        }

        if ($this->user) {
            $query->where('user_id', $this->user);
        }

        if ($this->start_date) {
            $query->whereDate('date', '>=', $this->start_date);
        }

        if ($this->end_date) {
            $query->whereDate('date', '<=', $this->end_date);
        }

        $this->data = [
            'total_sales' => $query->sum('total'),
            'invoice_count' => $query->count(),
            'total_discount' => $query->sum('discount'),
            'total_tax' => $query->sum('tax'),
            'retail_sales' => $query->where('sale_type', 'قطاعي')->sum('total'),
            'wholesale_sales' => $query->where('sale_type', 'جملة')->sum('total'),
        ];
    }
}
