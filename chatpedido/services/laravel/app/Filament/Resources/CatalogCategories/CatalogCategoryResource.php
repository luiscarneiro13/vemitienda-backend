<?php

namespace App\Filament\Resources\CatalogCategories;

use App\Domain\Catalog\Models\CatalogCategory;
use App\Filament\Resources\CatalogCategories\Pages\CreateCatalogCategory;
use App\Filament\Resources\CatalogCategories\Pages\EditCatalogCategory;
use App\Filament\Resources\CatalogCategories\Pages\ListCatalogCategories;
use App\Filament\Resources\CatalogCategories\Schemas\CatalogCategoryForm;
use App\Filament\Resources\CatalogCategories\Tables\CatalogCategoriesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CatalogCategoryResource extends Resource
{
    protected static ?string $model = CatalogCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|UnitEnum|null $navigationGroup = 'Catálogo';

    protected static ?string $navigationLabel = 'Categorías';

    protected static ?string $modelLabel = 'categoría';

    protected static ?string $pluralModelLabel = 'categorías';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return CatalogCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CatalogCategoriesTable::configure($table);
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
            'index' => ListCatalogCategories::route('/'),
            'create' => CreateCatalogCategory::route('/create'),
            'edit' => EditCatalogCategory::route('/{record}/edit'),
        ];
    }
}
