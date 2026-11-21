<?php

namespace App\Filament\Resources\CompanyModules;

use App\Core\Features\Models\CompanyModule;
use App\Filament\Resources\CompanyModules\Pages\CreateCompanyModule;
use App\Filament\Resources\CompanyModules\Pages\EditCompanyModule;
use App\Filament\Resources\CompanyModules\Pages\ListCompanyModules;
use App\Filament\Resources\CompanyModules\Schemas\CompanyModuleForm;
use App\Filament\Resources\CompanyModules\Tables\CompanyModulesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CompanyModuleResource extends Resource
{
    protected static ?string $model = CompanyModule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static string|UnitEnum|null $navigationGroup = 'Sistema';

    protected static ?string $navigationLabel = 'Feature Flags';

    protected static ?string $modelLabel = 'feature flag';

    public static function form(Schema $schema): Schema
    {
        return CompanyModuleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompanyModulesTable::configure($table);
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
            'index' => ListCompanyModules::route('/'),
            'create' => CreateCompanyModule::route('/create'),
            'edit' => EditCompanyModule::route('/{record}/edit'),
        ];
    }
}
