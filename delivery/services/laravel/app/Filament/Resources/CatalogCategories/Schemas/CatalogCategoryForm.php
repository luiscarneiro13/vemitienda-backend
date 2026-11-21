<?php

namespace App\Filament\Resources\CatalogCategories\Schemas;

use App\Filament\Forms\Components\CategoryIconPicker;
use App\Filament\Support\CatalogSelect;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CatalogCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Categoría del Menú')
                    ->columns(2)
                    ->schema([
                        CatalogSelect::make(withDefault: false)
                            ->columnSpanFull(),
                        TextInput::make('name')
                            ->label('Nombre de la categoría')
                            ->required()
                            ->maxLength(100)
                            ->columnSpanFull(),
                        CategoryIconPicker::make('icon')
                            ->label('Ícono o emoji de la categoría')
                            ->columnSpanFull()
                            ->helperText('Se muestra en las pestañas del menú del catálogo y junto a los platos.'),
                        TextInput::make('sort_order')
                            ->label('Orden')
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                        Toggle::make('is_active')
                            ->label('Visible en el catálogo')
                            ->default(true),
                        Textarea::make('description')
                            ->label('Descripción (opcional)')
                            ->placeholder('Breve reseña sobre los productos de esta sección...')
                            ->rows(2)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
