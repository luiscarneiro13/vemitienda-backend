<?php

namespace App\Filament\Resources\DatabaseNotifications\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Notifications\DatabaseNotification;

class DatabaseNotificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('notifiable_type')
                    ->label('Destinatario')
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-'),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-'),
                IconColumn::make('read_at')
                    ->label('Leída')
                    ->boolean()
                    ->getStateUsing(fn (DatabaseNotification $record): bool => $record->read_at !== null),
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('toggleRead')
                    ->label(fn (DatabaseNotification $record): string => $record->read_at ? 'Marcar como no leída' : 'Marcar como leída')
                    ->icon(fn (DatabaseNotification $record): string => $record->read_at ? 'heroicon-o-envelope' : 'heroicon-o-envelope-open')
                    ->action(fn (DatabaseNotification $record) => $record->read_at
                        ? $record->update(['read_at' => null])
                        : $record->update(['read_at' => now()])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
