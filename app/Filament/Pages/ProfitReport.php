<?php

namespace App\Filament\Pages;

use App\Models\SalesInvoice;
use App\Models\Expense;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class ProfitReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $title = 'تقرير الأرباح';
    protected static ?string $navigationLabel = 'تقرير الأرباح';
    protected static string $view = 'filament.pages.profit-report';

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
            Grid::make(3)->schema([
                Select::make('user')
                    ->label('الموظف')
                    ->options(User::pluck('name', 'id'))
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
        $salesQuery = SalesInvoice::query();
        $expensesQuery = Expense::query();

        if ($this->user) {
            $salesQuery->where('user_id', $this->user);
            $expensesQuery->where('user_id', $this->user);
        }

        if ($this->start_date) {
            $salesQuery->whereDate('date', '>=', $this->start_date);
            $expensesQuery->whereDate('date', '>=', $this->start_date);
        }

        if ($this->end_date) {
            $salesQuery->whereDate('date', '<=', $this->end_date);
            $expensesQuery->whereDate('date', '<=', $this->end_date);
        }

        $salesTotal = $salesQuery->sum('total');
        $expensesTotal = $expensesQuery->sum('amount');

        $this->data = [
            'sales' => $salesTotal,
            'expenses' => $expensesTotal,
            'profit' => $salesTotal - $expensesTotal,
        ];
    }
}

