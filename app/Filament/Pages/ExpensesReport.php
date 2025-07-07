<?php

namespace App\Filament\Pages;

use App\Models\Expense;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class ExpensesReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $title = 'تقرير المصروفات';
    protected static ?string $navigationLabel = 'تقرير المصروفات';
    protected static string $view = 'filament.pages.expenses-report';

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
                    ->label('نوع المصروف')
                    ->options(Expense::select('type')->distinct()->pluck('type', 'type')->toArray())
                    ->reactive(),

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
        $query = Expense::query();

        if ($this->type) {
            $query->where('type', $this->type);
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
            'total_expenses' => $query->sum('amount'),
            'count' => $query->count(),
        ];
    }
}

