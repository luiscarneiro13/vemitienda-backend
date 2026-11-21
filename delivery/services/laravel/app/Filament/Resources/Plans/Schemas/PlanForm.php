<?php

namespace App\Filament\Resources\Plans\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('price')
                    ->label('Precio')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('$'),
                Select::make('interval')
                    ->label('Periodicidad')
                    ->options(['monthly' => 'Mensual', 'yearly' => 'Anual'])
                    ->default('monthly')
                    ->required(),
                TagsInput::make('features')
                    ->label('Características'),
                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
            ]);
    }
}
