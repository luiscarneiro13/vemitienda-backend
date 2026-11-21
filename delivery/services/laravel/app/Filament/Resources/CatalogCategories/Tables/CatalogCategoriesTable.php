<?php

namespace App\Filament\Resources\CatalogCategories\Tables;

use App\Domain\Catalog\Models\CatalogCategory;
use App\Filament\Support\CatalogSelect;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CatalogCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('icon')
                    ->label('Ícono')
                    ->toggleable(),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('catalog.company.name')
                    ->label('Tienda')
                    ->visible(fn (): bool => CatalogSelect::isSuperAdmin()),
                TextColumn::make('products_count')
                    ->label('Productos')
                    ->counts(['products' => fn (Builder $query): Builder => $query->withoutTenancy()])
                    ->badge()
                    ->color('gray'),
                IconColumn::make('is_active')
                    ->label('Visible')
                    ->boolean(),
                TextColumn::make('sort_order')
                    ->label('Orden')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->hidden(fn (CatalogCategory $record): bool => ($record->products_count ?? $record->products()->withoutTenancy()->count()) > 0),
            ])
            ->toolbarActions([])
            ->reorderable('sort_order')
            ->defaultSort('sort_order');
    }
}
