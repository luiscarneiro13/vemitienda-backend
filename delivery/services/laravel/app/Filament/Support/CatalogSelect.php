<?php

namespace App\Filament\Support;

use App\Core\Tenancy\TenantContext;
use App\Domain\Catalog\Models\Catalog;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;

class CatalogSelect
{
    public static function make(bool $withDefault = true): Select
    {
        $select = Select::make('catalog_id')
            ->label('Catálogo')
            ->placeholder('Selecciona un catálogo...')
            ->relationship('catalog', 'name', fn (Builder $query): Builder => static::scope($query))
            ->getOptionLabelFromRecordUsing(fn (Catalog $record): string => static::isSuperAdmin()
                ? $record->company?->name.' — '.$record->name
                : $record->name)
            ->searchable()
            ->preload()
            ->required()
            ->validationMessages([
                'required' => 'Debes seleccionar un catálogo.',
            ]);

        if ($withDefault) {
            $select->default(fn (): ?int => app(TenantContext::class)->company()?->defaultCatalog()->withoutTenancy()->value('id'));
        }

        return $select;
    }

    /** Filtro explícito por tienda (además del CompanyScope). */
    public static function scope(Builder $query): Builder
    {
        return static::isSuperAdmin()
            ? $query
            : $query->where('company_id', app(TenantContext::class)->companyId());
    }

    public static function isSuperAdmin(): bool
    {
        return (bool) auth()->user()?->hasRole('super_admin');
    }
}
