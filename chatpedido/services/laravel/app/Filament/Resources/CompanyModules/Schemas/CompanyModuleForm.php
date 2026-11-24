<?php

namespace App\Filament\Resources\CompanyModules\Schemas;

use App\Filament\Resources\Companies\CompanyResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class CompanyModuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('company_id')
                    ->label('Empresa')
                    ->relationship('company', 'name', fn (Builder $query): Builder => CompanyResource::scopeToUser($query))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('module_id')
                    ->label('Módulo')
                    ->relationship('module', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Toggle::make('enabled')
                    ->label('Habilitado')
                    ->default(true),
            ]);
    }
}
