<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Filament\Resources\Companies\CompanyResource;
use App\Support\RoleLabel;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                TextInput::make('email')
                    ->label('Correo electrónico')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
                Select::make('company_id')
                    ->label('Empresa')
                    ->relationship('company', 'name', fn (Builder $query): Builder => CompanyResource::scopeToUser($query))
                    ->default(fn () => tenant()->companyId() ?? auth()->user()?->company_id)
                    ->searchable()
                    ->preload(),
                Select::make('current_company_id')
                    ->label('Empresa activa')
                    ->relationship('currentCompany', 'name', fn (Builder $query): Builder => CompanyResource::scopeToUser($query))
                    ->default(fn () => tenant()->companyId() ?? auth()->user()?->current_company_id)
                    ->searchable()
                    ->preload(),
                Select::make('roles')
                    ->label('Roles')
                    ->relationship(
                        'roles',
                        'name',
                        fn (Builder $query): Builder => $query->when(
                            ! auth()->user()?->hasRole('super_admin'),
                            fn (Builder $q): Builder => $q->whereNotIn('name', ['super_admin', 'store_owner', 'admin', 'manager'])
                        )
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record): string => RoleLabel::for($record->name))
                    ->multiple()
                    ->preload()
                    ->searchable(),
            ]);
    }
}
