<?php

namespace App\Filament\Resources\Catalogs\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CatalogForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(150),
                Textarea::make('description')
                    ->label('Descripción')
                    ->maxLength(1000),
                Toggle::make('is_active')
                    ->label('Publicado')
                    ->helperText('Si lo desactivas, los enlaces de todas las sucursales mostrarán la página de error.'),
            ]);
    }
}
