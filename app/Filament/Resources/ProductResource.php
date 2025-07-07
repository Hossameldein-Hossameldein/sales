<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    public static function getModelLabel(): string
    {
        return 'منتج';
    }

    public static function getPluralLabel(): string
    {
        return 'المنتجات';
    }

    public static function getNavigationLabel(): string
    {
        return 'المنتجات';
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->label('اسم المنتج')->required(),
                Forms\Components\TextInput::make('barcode')->label('الباركود'),
                Forms\Components\Select::make('category_id')
                    ->label('الصنف')
                    ->relationship('category', 'name')
                    ->searchable(),
                Forms\Components\TextInput::make('unit')->label('الوحدة')->default('قطعة'),
                Forms\Components\TextInput::make('purchase_price')->label('سعر الشراء')->numeric()->required(),
                Forms\Components\TextInput::make('retail_price')->label('سعر البيع القطاعي')->numeric()->required(),
                Forms\Components\TextInput::make('wholesale_price')->label('سعر البيع الجملة')->numeric()->required(),
                Forms\Components\TextInput::make('stock')->label('الرصيد الحالي')->numeric()->default(0),
                Forms\Components\Toggle::make('has_expiry')->label('له صلاحية؟'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('الاسم'),
                Tables\Columns\TextColumn::make('barcode')->label('الباركود'),
                Tables\Columns\TextColumn::make('category.name')->label('الصنف'),
                Tables\Columns\TextColumn::make('stock')->label('الرصيد')->sortable(),
                Tables\Columns\TextColumn::make('purchase_price')->label('سعر الشراء')->money('EGP'),
                Tables\Columns\TextColumn::make('retail_price')->label('سعر البيع قطاعي')->money('EGP'),
                Tables\Columns\TextColumn::make('wholesale_price')->label('سعر البيع جملة')->money('EGP'),
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
            'index' => Pages\ListProducts::route('/'),
            // 'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
