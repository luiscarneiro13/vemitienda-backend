<?php

namespace App\Filament\Resources\TimelineEntries\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TimelineEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->default('Sistema')
                    ->searchable(),
                TextColumn::make('description')
                    ->label('Descripción')
                    ->wrap()
                    ->searchable(),
                TextColumn::make('subject_type')
                    ->label('Modelo')
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
