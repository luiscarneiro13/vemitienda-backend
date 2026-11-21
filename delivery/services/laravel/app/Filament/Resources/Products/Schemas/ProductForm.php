<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Domain\Catalog\Enums\OptionSelectionType;
use App\Domain\Catalog\Models\Catalog;
use App\Filament\Support\CatalogSelect;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Datos del producto')->columns(2)->schema([
                CatalogSelect::make()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('catalog_category_id', null)),
                Select::make('catalog_category_id')
                    ->label('Categoría')
                    ->relationship('category', 'name', fn (Builder $query, Get $get): Builder => CatalogSelect::scope($query)->where('catalog_id', $get('catalog_id')))
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('name')->label('Nombre')->required()->maxLength(150),
                TextInput::make('price')
                    ->label('Precio de venta')
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->step(0.01)
                    ->prefix(fn (Get $get): string => Catalog::withoutTenancy()->find($get('catalog_id'))?->company?->currency ?: 'USD'),
                TextInput::make('compare_at_price')
                    ->label('Precio regular / tachado (opcional)')
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->helperText('Se mostrará tachado junto al precio actual para indicar descuento.')
                    ->prefix(fn (Get $get): string => Catalog::withoutTenancy()->find($get('catalog_id'))?->company?->currency ?: 'USD'),
                TextInput::make('badge')
                    ->label('Etiqueta / Badge (opcional)')
                    ->placeholder('Ej: Más Vendido, Picante 🔥, 2x1, Nuevo')
                    ->maxLength(50),
                Toggle::make('is_featured')
                    ->label('Producto destacado')
                    ->helperText('Aparece en la sección destacada superior del catálogo.')
                    ->default(false),
                Textarea::make('description')->label('Descripción')->maxLength(1000)->columnSpanFull(),
                FileUpload::make('image_path')
                    ->label('Imagen')
                    ->image()
                    ->disk('public')
                    ->directory('products')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->imageEditor()
                    ->columnSpanFull(),
                TextInput::make('image_url')
                    ->label('o URL de imagen externa')
                    ->url()
                    ->rules(['nullable', 'url:https'])
                    ->maxLength(2048)
                    ->helperText('Si se indica, tiene prioridad sobre la imagen subida.')
                    ->columnSpanFull(),
                Toggle::make('is_active')->label('Visible en el catálogo')->default(true),
                Toggle::make('is_available')->label('Disponible (desactivar = Agotado)')->default(true),
                TextInput::make('sort_order')->label('Orden')->numeric()->minValue(0)->default(0),
            ]),
            Section::make('Opciones y extras')->schema([
                Repeater::make('optionGroups')
                    ->label('Grupos de opciones')
                    ->relationship()
                    ->reorderable()
                    ->orderColumn('sort_order')
                    ->collapsible()
                    ->defaultItems(0)
                    ->addActionLabel('Agregar grupo')
                    ->itemLabel(fn (array $state): string => $state['name'] ?? 'Grupo')
                    ->schema([
                        TextInput::make('name')->label('Nombre del grupo')->placeholder('Ej: Tamaño, Extras, Sin ingredientes')->required()->maxLength(100),
                        Toggle::make('is_removal')
                            ->label('Quita ingredientes')
                            ->helperText('Las opciones no suman precio (ej: sin cebolla, sin salsa).')
                            ->default(false)
                            ->live(),
                        Select::make('selection_type')
                            ->label('Tipo')
                            ->options(collect(OptionSelectionType::cases())->mapWithKeys(fn (OptionSelectionType $c) => [$c->value => $c->label()])->all())
                            ->default(OptionSelectionType::Single->value)
                            ->required()
                            ->live()
                            ->visible(fn (Get $get): bool => ! $get('is_removal')),
                        Toggle::make('is_required')->label('Obligatorio')->default(false)
                            ->visible(fn (Get $get): bool => ! $get('is_removal')),
                        TextInput::make('max_selections')
                            ->label('Máximo a elegir (vacío = sin límite)')
                            ->numeric()
                            ->minValue(1)
                            ->visible(fn (Get $get): bool => $get('selection_type') === OptionSelectionType::Multiple->value && ! $get('is_removal')),
                        Repeater::make('options')
                            ->label('Opciones')
                            ->relationship()
                            ->reorderable()
                            ->orderColumn('sort_order')
                            ->addActionLabel('Agregar opción')
                            ->schema([
                                TextInput::make('name')->label('Opción')->required()->maxLength(100)->placeholder(fn (Get $get): string => $get('../../is_removal') ? 'Ej: Sin cebolla' : 'Ej: Queso extra'),
                                TextInput::make('price')->label('Precio adicional')->numeric()->default(0)->minValue(0)->step(0.01)
                                    ->visible(fn (Get $get): bool => ! $get('../../is_removal')),
                                Toggle::make('is_active')->label('Activa')->default(true),
                            ])
                            ->columns(3)
                            ->minItems(1)
                            ->defaultItems(1),
                    ]),
            ]),
        ]);
    }
}
