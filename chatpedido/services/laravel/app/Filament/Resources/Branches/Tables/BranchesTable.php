<?php

namespace App\Filament\Resources\Branches\Tables;

use App\Core\Branches\Models\Branch;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BranchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->with('company:id,name,slug'))
            ->columns([
                TextColumn::make('company.name')
                    ->label('Empresa')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('cityRecord.name')
                    ->label('Ciudad')
                    ->searchable(),
                TextColumn::make('whatsapp_number')
                    ->label('WhatsApp'),
                TextColumn::make('country')
                    ->label('País')
                    ->searchable(),
                IconColumn::make('is_main')
                    ->label('Principal')
                    ->boolean(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Activa',
                        'inactive' => 'Inactiva',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        default => 'gray',
                    }),
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
                Action::make('openCatalog')
                    ->label('Ver catálogo')
                    ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
                    ->url(fn (Branch $record): ?string => $record->publicCatalogUrl())
                    ->openUrlInNewTab()
                    ->visible(fn (Branch $record): bool => $record->isPubliclyAvailable()),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
