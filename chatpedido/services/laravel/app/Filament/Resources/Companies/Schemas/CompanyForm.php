<?php

namespace App\Filament\Resources\Companies\Schemas;

use App\Domain\Locations\Models\City;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Identidad y Datos Fiscales')
                    ->description('Nombre comercial, RIF y correos de contacto.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre de la tienda')
                            ->required()
                            ->maxLength(100)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                        TextInput::make('slug')
                            ->label('Slug (Enlace público)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->disabled(fn (): bool => ! auth()->user()?->hasRole('super_admin'))
                            ->helperText('Forma parte de la URL de tu catálogo público; solo el super administrador puede modificarlo.'),
                        TextInput::make('tax_id')
                            ->label('RIF / Identificación Fiscal')
                            ->placeholder('Ej: J-12345678-9')
                            ->maxLength(50),
                        TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email(),
                        TextInput::make('phone')
                            ->label('Teléfono fijo / secundario')
                            ->tel(),
                        Toggle::make('is_active')
                            ->label('Tienda activa en el sistema')
                            ->default(true)
                            ->visible(fn (): bool => auth()->user()?->hasRole('super_admin') ?? false),
                    ]),

                Section::make('Recepción de Pedidos y Catálogo Web')
                    ->description('Canal de WhatsApp para pedidos de clientes, ciudad y enlace público del catálogo.')
                    ->columns(2)
                    ->schema([
                        TextInput::make('whatsapp_number')
                            ->label('WhatsApp para pedidos')
                            ->helperText('Los pedidos armados por tus clientes en la web se enviarán a este número. Incluye código de país (ej: 584141234567).')
                            ->tel()
                            ->regex('/^\+?[\d\s\-()]+$/')
                            ->rule(fn () => function (string $attribute, $value, \Closure $fail): void {
                                if (blank($value)) {
                                    return;
                                }
                                $digits = preg_replace('/\D+/', '', (string) $value);
                                if (strlen($digits) < 10 || strlen($digits) > 15) {
                                    $fail('El WhatsApp debe tener entre 10 y 15 dígitos, incluyendo el código de país.');
                                }
                            }),
                        Select::make('city_id')
                            ->label('Ciudad')
                            ->options(fn () => City::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->disabled(fn (): bool => ! auth()->user()?->hasRole('super_admin'))
                            ->helperText('Ciudad asignada a la tienda. Solo el super administrador puede modificarla.'),
                        Textarea::make('address')
                            ->label('Dirección del local')
                            ->placeholder('Ej: Av. Principal, C.C. Los Naranjos, Nivel Feria, Local 4B')
                            ->rows(2)
                            ->maxLength(500)
                            ->columnSpanFull(),
                        TextInput::make('public_catalog_url')
                            ->label('Enlace de tu catálogo web para clientes')
                            ->helperText('Comparte este link con tus clientes en WhatsApp, Instagram o tus redes para que vean tu menú y te envíen pedidos.')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull()
                            ->suffixAction(
                                Action::make('openCatalog')
                                    ->icon('heroicon-o-arrow-top-right-on-square')
                                    ->tooltip('Abrir catálogo web')
                                    ->url(fn ($state) => $state, shouldOpenInNewTab: true)
                                    ->visible(fn ($state) => ! empty($state))
                            ),
                    ]),

                Section::make('Marca, Logo y Presentación')
                    ->description('Logo oficial, descripción y fotos que verán los clientes en el catálogo y en el directorio.')
                    ->columns(2)
                    ->schema([
                        FileUpload::make('logo')
                            ->label('Logo de la tienda')
                            ->image()
                            ->avatar()
                            ->disk('public')
                            ->directory('logos')
                            ->visibility('public')
                            ->imageResizeMode('cover')
                            ->maxSize(2048)
                            ->helperText('Sube el logo de tu tienda en formato PNG, JPG o SVG (máx. 2MB). Se muestra en tu catálogo web y en el directorio.'),
                        TextInput::make('store_category')
                            ->label('Categoría del comercio')
                            ->placeholder('Ej: Hamburguesas, Pizzería, Cafetería, Farmacia, Ferretería')
                            ->maxLength(100),
                        Textarea::make('store_description')
                            ->label('Descripción de la tienda')
                            ->placeholder('Describe brevemente tu especialidad, propuesta culinaria o productos...')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull()
                            ->helperText('Se muestra en la portada de ChatPedido y en la cabecera de información del catálogo.'),
                        TextInput::make('cover_image_url')
                            ->label('URL de la foto de portada (opcional)')
                            ->url()
                            ->rules(['nullable', 'url:https'])
                            ->maxLength(2048)
                            ->helperText('Enlace HTTPS a una imagen pública para la tarjeta de tu tienda en el directorio.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
