<?php

namespace App\Filament\Resources\Modules\Tables;

use App\Core\Modules\Models\Module;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ModulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),
                TextColumn::make('version')
                    ->label('Versión'),
                IconColumn::make('enabled')
                    ->label('Habilitado')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Registrado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('toggleEnabled')
                    ->label(fn (Module $record): string => $record->enabled ? 'Deshabilitar' : 'Habilitar')
                    ->icon(fn (Module $record): string => $record->enabled ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Module $record): string => $record->enabled ? 'danger' : 'success')
                    ->action(fn (Module $record) => $record->update(['enabled' => ! $record->enabled])),
                DeleteAction::make(),
            ])
            ->toolbarActions([]);
    }
}
