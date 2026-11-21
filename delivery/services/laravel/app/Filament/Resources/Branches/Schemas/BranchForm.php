<?php

namespace App\Filament\Resources\Branches\Schemas;

use App\Core\Branches\Models\Branch;
use App\Filament\Resources\Companies\CompanyResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos de la Sucursal y WhatsApp de Pedidos')
                    ->columns(2)
                    ->schema([
                        Select::make('company_id')
                            ->label('Empresa')
                            ->helperText('Por el momento cada empresa puede tener una sola sucursal.')
                            ->relationship('company', 'name', fn (Builder $query): Builder => CompanyResource::scopeToUser($query))
                            ->searchable()
                            ->preload()
                            ->required(fn (): bool => (bool) auth()->user()?->hasRole('super_admin'))
                            ->disabled(fn (): bool => ! auth()->user()?->hasRole('super_admin')),
                        TextInput::make('name')
                            ->label('Nombre')
                            ->required(),
                        Select::make('city_id')
                            ->label('Ciudad')
                            ->relationship('cityRecord', 'name', fn (Builder $query) => $query->where('is_active', true))
                            ->searchable()
                            ->preload()
                            ->required(fn (): bool => (bool) auth()->user()?->hasRole('super_admin'))
                            ->disabled(fn (): bool => ! auth()->user()?->hasRole('super_admin')),
                        TextInput::make('address')
                            ->label('Dirección'),
                        TextInput::make('whatsapp_number')
                            ->label('WhatsApp para pedidos')
                            ->helperText('Solo números, con código de país y sin "+". Ej: 584141234567')
                            ->tel()
                            ->required()
                            ->regex('/^\+?[\d\s\-()]+$/')
                            ->rule(fn () => function (string $attribute, $value, \Closure $fail): void {
                                $digits = preg_replace('/\D+/', '', (string) $value);
                                if (strlen($digits) < 10 || strlen($digits) > 15) {
                                    $fail('El WhatsApp debe tener entre 10 y 15 dígitos, incluyendo el código de país.');
                                }
                            }),
                        TextInput::make('phone')
                            ->label('Teléfono')
                            ->tel(),
                        TextInput::make('email')
                            ->label('Correo electrónico')
                            ->email(),
                        TextInput::make('country')
                            ->label('País'),
                        TextInput::make('timezone')
                            ->label('Zona horaria'),
                        Select::make('status')
                            ->label('Estado')
                            ->options(['active' => 'Activa', 'inactive' => 'Inactiva'])
                            ->default('active')
                            ->required(fn (): bool => (bool) auth()->user()?->hasRole('super_admin'))
                            ->disabled(fn (): bool => ! auth()->user()?->hasRole('super_admin')),
                        Toggle::make('is_main')
                            ->label('Sede principal')
                            ->default(false)
                            ->disabled(fn (): bool => ! auth()->user()?->hasRole('super_admin')),
                        TextInput::make('public_catalog_url')
                            ->label('Enlace del catálogo para esta sucursal')
                            ->helperText('Comparte este enlace con tus clientes por WhatsApp.')
                            ->formatStateUsing(fn (?Branch $record): ?string => $record?->publicCatalogUrl())
                            ->disabled()
                            ->dehydrated(false)
                            ->copyable()
                            ->visibleOn('edit')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
