<?php

namespace App\Filament\Resources\Catalogs\Tables;

use App\Filament\Support\CatalogSelect;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CatalogsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')
                    ->label('Tienda')
                    ->searchable()
                    ->visible(fn (): bool => CatalogSelect::isSuperAdmin()),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                IconColumn::make('is_default')
                    ->label('Por defecto')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label('Publicado')
                    ->boolean(),
                TextColumn::make('categories_count')
                    ->label('Categorías')
                    ->counts('categories')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('products_count')
                    ->label('Productos')
                    ->counts('products')
                    ->badge()
                    ->color('gray'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([]);
    }
}
