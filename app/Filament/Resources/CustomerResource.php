<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getModelLabel(): string
    {
        return 'عميل';
    }

    public static function getPluralLabel(): string
    {
        return 'العملاء';
    }
    public static function getNavigationLabel(): string
    {
        return 'العملاء';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('الاسم')
                    ->required(),

                TextInput::make('phone')
                    ->label('رقم التليفون'),

                TextInput::make('email')
                    ->label('البريد الإلكتروني')
                    ->email(),

                TextInput::make('address')
                    ->label('العنوان'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('الاسم')->searchable(),
                TextColumn::make('phone')->label('التليفون'),
                TextColumn::make('email')->label('البريد الإلكتروني'),
                TextColumn::make('address')->label('العنوان')->limit(30),
                TextColumn::make('created_at')->label('تم الإنشاء في')->dateTime('d-m-Y'),
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
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
