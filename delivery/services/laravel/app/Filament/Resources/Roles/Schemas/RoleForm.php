<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Support\PermissionLabel;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Permission;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->unique(ignoreRecord: true),
                Hidden::make('guard_name')
                    ->default('web'),
                CheckboxList::make('permissions')
                    ->label('Permisos')
                    ->relationship('permissions', 'name')
                    ->options(fn () => Permission::query()->pluck('name', 'id')
                        ->map(fn (string $name): string => PermissionLabel::for($name)))
                    ->searchable()
                    ->columns(2)
                    ->bulkToggleable(),
            ]);
    }
}
