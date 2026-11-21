<?php

namespace App\Filament\Resources\Products\Tables;

use App\Domain\Catalog\Models\Product;
use App\Filament\Support\CatalogSelect;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('company:id,currency'))
            ->columns([
                ImageColumn::make('image_path')
                    ->label('')
                    ->state(fn (Product $record): ?string => $record->imageUrl())
                    ->circular()
                    ->imageSize(40),
                TextColumn::make('name')->label('Nombre')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Categoría'),
                TextColumn::make('price')->label('Precio')->money(fn (Product $record): string => $record->company?->currency ?: 'USD'),
                IconColumn::make('is_featured')->label('Destacado')->boolean()->toggleable(),
                TextColumn::make('badge')->label('Etiqueta')->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')->label('Visible')->boolean(),
                ToggleColumn::make('is_available')->label('Disponible'),
                TextColumn::make('catalog.company.name')->label('Tienda')->visible(fn (): bool => CatalogSelect::isSuperAdmin()),
            ])
            ->filters([
                SelectFilter::make('catalog_category_id')->label('Categoría')->relationship('category', 'name', fn (Builder $query): Builder => CatalogSelect::scope($query)),
                TernaryFilter::make('is_available')->label('Disponible'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order')
            ->emptyStateHeading('Aún no hay productos en el catálogo')
            ->emptyStateDescription('Agrega tu primer plato o producto para que aparezca en el menú público de WhatsApp.');
    }
}
