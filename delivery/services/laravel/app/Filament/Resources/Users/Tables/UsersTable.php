<?php

namespace App\Filament\Resources\Users\Tables;

use App\Support\RoleLabel;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Correo electrónico')
                    ->searchable(),
                TextColumn::make('company.name')
                    ->label('Empresa')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->formatStateUsing(fn (string $state): string => RoleLabel::for($state))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'primary',
                        'store_owner' => 'warning',
                        'store_admin' => 'info',
                        default => 'gray',
                    }),
                TextColumn::make('email_verified_at')
                    ->label('Verificado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
