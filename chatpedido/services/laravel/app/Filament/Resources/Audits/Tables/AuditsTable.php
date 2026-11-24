<?php

namespace App\Filament\Resources\Audits\Tables;

use Filament\Actions\Action;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuditsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->default('Sistema')
                    ->searchable(),
                TextColumn::make('event')
                    ->label('Acción')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'created' => 'Creación',
                        'updated' => 'Actualización',
                        'deleted' => 'Eliminación',
                        'login' => 'Inicio de sesión',
                        'logout' => 'Cierre de sesión',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        'login' => 'primary',
                        'logout' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('auditable_type')
                    ->label('Modelo')
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-')
                    ->searchable(),
                TextColumn::make('auditable_id')
                    ->label('ID registro'),
                TextColumn::make('ip_address')
                    ->label('IP'),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('event')
                    ->label('Acción')
                    ->options([
                        'created' => 'Creación',
                        'updated' => 'Actualización',
                        'deleted' => 'Eliminación',
                        'login' => 'Inicio de sesión',
                        'logout' => 'Cierre de sesión',
                    ]),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('Ver detalle')
                    ->icon('heroicon-o-eye')
                    ->infolist([
                        TextEntry::make('user.name')->label('Usuario')->default('Sistema'),
                        TextEntry::make('event')->label('Acción'),
                        TextEntry::make('auditable_type')->label('Modelo'),
                        TextEntry::make('created_at')->label('Fecha')->dateTime(),
                        TextEntry::make('ip_address')->label('IP'),
                        KeyValueEntry::make('old_values')->label('Valores anteriores'),
                        KeyValueEntry::make('new_values')->label('Valores nuevos'),
                    ])
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),
            ])
            ->toolbarActions([]);
    }
}
