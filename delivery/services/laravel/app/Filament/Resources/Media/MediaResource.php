<?php

namespace App\Filament\Resources\Media;

use App\Core\Media\Models\Media;
use App\Filament\Resources\Media\Pages\ListMedia;
use App\Filament\Resources\Media\Schemas\MediaForm;
use App\Filament\Resources\Media\Tables\MediaTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Sistema';

    protected static ?string $navigationLabel = 'Media';

    protected static ?string $modelLabel = 'archivo';

    public static function form(Schema $schema): Schema
    {
        return MediaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MediaTable::configure($table);
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
            'index' => ListMedia::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        // Los adjuntos se suben vía MediaManager desde el formulario de su
        // recurso propietario (p. ej. logo de una empresa), no desde aquí.
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }
}
