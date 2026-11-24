<?php

namespace App\Filament\Resources\DatabaseNotifications\Pages;

use App\Filament\Resources\DatabaseNotifications\DatabaseNotificationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDatabaseNotifications extends ListRecords
{
    protected static string $resource = DatabaseNotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
