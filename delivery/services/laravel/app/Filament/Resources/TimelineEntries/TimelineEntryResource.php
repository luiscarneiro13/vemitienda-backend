<?php

namespace App\Filament\Resources\TimelineEntries;

use App\Core\Timeline\Models\TimelineEntry;
use App\Filament\Resources\TimelineEntries\Pages\ListTimelineEntries;
use App\Filament\Resources\TimelineEntries\Schemas\TimelineEntryForm;
use App\Filament\Resources\TimelineEntries\Tables\TimelineEntriesTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class TimelineEntryResource extends Resource
{
    protected static ?string $model = TimelineEntry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|UnitEnum|null $navigationGroup = 'Auditoría';

    protected static ?string $navigationLabel = 'Timeline';

    protected static ?string $modelLabel = 'evento';

    protected static ?string $pluralModelLabel = 'Timeline';

    public static function form(Schema $schema): Schema
    {
        return TimelineEntryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TimelineEntriesTable::configure($table);
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
            'index' => ListTimelineEntries::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }
}
