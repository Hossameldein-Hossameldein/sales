<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Filament\Resources\ExpenseResource\RelationManagers;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'المصروفات';
    protected static ?string $pluralModelLabel = 'المصروفات';
    protected static ?string $modelLabel = 'مصروف';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('type')
                ->label('نوع المصروف')
                ->options([
                    'كهرباء' => 'كهرباء',
                    'مياه' => 'مياه',
                    'مرتبات' => 'مرتبات',
                    'إيجار' => 'إيجار',
                    'أخرى' => 'أخرى',
                ])
                ->required(),

            TextInput::make('amount')
                ->label('المبلغ')
                ->numeric()
                ->required(),

            DatePicker::make('date')->label('التاريخ')->default(now())->required(),

            Textarea::make('notes')->label('ملاحظات'),

       
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')->label('نوع المصروف'),
                TextColumn::make('amount')->label('المبلغ')->money('EGP'),
                TextColumn::make('date')->label('التاريخ')->date(),
                TextColumn::make('notes')->label('ملاحظات')->limit(20),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
