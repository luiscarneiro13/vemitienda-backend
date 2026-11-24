<?php

namespace App\Filament\Resources\DatabaseNotifications;

use App\Filament\Resources\DatabaseNotifications\Pages\ListDatabaseNotifications;
use App\Filament\Resources\DatabaseNotifications\Schemas\DatabaseNotificationForm;
use App\Filament\Resources\DatabaseNotifications\Tables\DatabaseNotificationsTable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;
use UnitEnum;

class DatabaseNotificationResource extends Resource
{
    protected static ?string $model = DatabaseNotification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBell;

    protected static string|UnitEnum|null $navigationGroup = 'Sistema';

    protected static ?string $navigationLabel = 'Notificaciones';

    protected static ?string $modelLabel = 'notificación';

    public static function form(Schema $schema): Schema
    {
        return DatabaseNotificationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DatabaseNotificationsTable::configure($table);
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
            'index' => ListDatabaseNotifications::route('/'),
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
}
