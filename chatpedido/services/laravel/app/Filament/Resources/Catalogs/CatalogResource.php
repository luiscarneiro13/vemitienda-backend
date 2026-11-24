<?php

namespace App\Filament\Resources\Catalogs;

use App\Domain\Catalog\Models\Catalog;
use App\Filament\Resources\Catalogs\Pages\EditCatalog;
use App\Filament\Resources\Catalogs\Pages\ListCatalogs;
use App\Filament\Resources\Catalogs\Schemas\CatalogForm;
use App\Filament\Resources\Catalogs\Tables\CatalogsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CatalogResource extends Resource
{
    protected static ?string $model = Catalog::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|UnitEnum|null $navigationGroup = 'Catálogo';

    protected static ?string $navigationLabel = 'Catálogos';

    protected static ?string $modelLabel = 'catálogo';

    protected static ?string $pluralModelLabel = 'catálogos';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return CatalogForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CatalogsTable::configure($table);
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
            'index' => ListCatalogs::route('/'),
            'edit' => EditCatalog::route('/{record}/edit'),
        ];
    }
}
