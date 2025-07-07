<?php

namespace App\Filament\Pages;

use App\Models\SalesReturn;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class ReturnsReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-left';
    protected static ?string $title = 'تقرير المرتجعات';
    protected static ?string $navigationLabel = 'تقرير المرتجعات' ;
    protected static string $view = 'filament.pages.returns-report';

    public ?string $user = null;
    public ?string $start_date = null;
    public ?string $end_date = null;

    public $returns = [];

    public function mount()
    {
        $this->loadReturns();
    }

    public function updated($property)
    {
        $this->loadReturns();
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
            ]),
        ]);
    }

    public function loadReturns()
    {
        $query = SalesReturn::with(['user', 'invoice', 'items']);

        if ($this->user) {
            $query->where('user_id', $this->user);
        }

        if ($this->start_date) {
            $query->whereDate('created_at', '>=', $this->start_date);
        }

        if ($this->end_date) {
            $query->whereDate('created_at', '<=', $this->end_date);
        }

        $this->returns = $query->orderBy('created_at', 'desc')->get();
    }
}

