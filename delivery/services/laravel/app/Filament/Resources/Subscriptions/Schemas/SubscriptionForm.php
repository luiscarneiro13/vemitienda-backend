<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use App\Filament\Resources\Companies\CompanyResource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class SubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('company_id')
                    ->label('Empresa')
                    ->relationship('company', 'name', fn (Builder $query): Builder => CompanyResource::scopeToUser($query))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('plan_id')
                    ->label('Plan')
                    ->relationship('plan', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('status')
                    ->label('Estado')
                    ->options([
                        'trialing' => 'En periodo de prueba',
                        'active' => 'Activa',
                        'canceled' => 'Cancelada',
                        'expired' => 'Expirada',
                    ])
                    ->default('trialing')
                    ->required(),
                DateTimePicker::make('trial_ends_at')
                    ->label('Fin del periodo de prueba'),
                DateTimePicker::make('starts_at')
                    ->label('Inicio'),
                DateTimePicker::make('ends_at')
                    ->label('Fin'),
            ]);
    }
}
