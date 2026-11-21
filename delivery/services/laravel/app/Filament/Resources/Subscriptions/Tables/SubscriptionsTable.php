<?php

namespace App\Filament\Resources\Subscriptions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')
                    ->label('Empresa')
                    ->searchable(),
                TextColumn::make('plan.name')
                    ->label('Plan')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'trialing' => 'En periodo de prueba',
                        'active' => 'Activa',
                        'canceled' => 'Cancelada',
                        'expired' => 'Expirada',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'trialing' => 'warning',
                        'canceled', 'expired' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('trial_ends_at')
                    ->label('Fin de prueba')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('starts_at')
                    ->label('Inicio')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->label('Fin')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
