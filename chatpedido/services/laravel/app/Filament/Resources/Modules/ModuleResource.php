<?php

namespace App\Filament\Resources\Modules;

use App\Core\Modules\Models\Module;
use App\Filament\Resources\Modules\Pages\ListModules;
use App\Filament\Resources\Modules\Schemas\ModuleForm;
use App\Filament\Resources\Modules\Tables\ModulesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ModuleResource extends Resource
{
    protected static ?string $model = Module::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPuzzlePiece;

    protected static string|UnitEnum|null $navigationGroup = 'Sistema';

    protected static ?string $navigationLabel = 'Módulos';

    protected static ?string $modelLabel = 'módulo';

    public static function form(Schema $schema): Schema
    {
        return ModuleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ModulesTable::configure($table);
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
            'index' => ListModules::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        // Los módulos se registran vía discovery (Modules/*/module.json) o
        // los comandos artisan starter:module-*, nunca a mano en el panel.
        return false;
    }
}
