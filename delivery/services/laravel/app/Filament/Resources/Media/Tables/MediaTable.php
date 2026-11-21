<?php

namespace App\Filament\Resources\Media\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MediaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('original_name')
                    ->label('Archivo')
                    ->searchable(),
                TextColumn::make('collection')
                    ->label('Colección')
                    ->badge(),
                TextColumn::make('model_type')
                    ->label('Modelo')
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-'),
                TextColumn::make('mime_type')
                    ->label('Tipo'),
                TextColumn::make('size')
                    ->label('Tamaño')
                    ->formatStateUsing(fn (int $state): string => number_format($state / 1024, 1).' KB')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Subido')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('download')
                    ->label('Descargar')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn ($record) => $record->url())
                    ->openUrlInNewTab(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
