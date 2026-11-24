<?php

namespace App\Filament\Resources\Companies;

use App\Core\Companies\Models\Company;
use App\Core\Tenancy\TenantContext;
use App\Filament\Resources\Companies\Pages\CreateCompany;
use App\Filament\Resources\Companies\Pages\EditCompany;
use App\Filament\Resources\Companies\Pages\ListCompanies;
use App\Filament\Resources\Companies\Schemas\CompanyForm;
use App\Filament\Resources\Companies\Tables\CompaniesTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    protected static string|UnitEnum|null $navigationGroup = 'Empresa';

    protected static ?string $navigationLabel = 'Tiendas';

    public static function getNavigationLabel(): string
    {
        return auth()->user()?->hasRole('super_admin') ? 'Tiendas' : 'Mi Tienda';
    }

    public static function getNavigationUrl(): string
    {
        /** @var User|null $user */
        $user = auth()->user();

        if ($user && ! $user->hasRole('super_admin')) {
            $companyId = app(TenantContext::class)->companyId()
                ?? $user->current_company_id
                ?? $user->companies()->value('companies.id');

            if ($companyId) {
                return static::getUrl('edit', ['record' => $companyId]);
            }
        }

        return static::getUrl('index');
    }

    protected static ?string $modelLabel = 'tienda';

    protected static ?string $pluralModelLabel = 'tiendas';

    public static function getEloquentQuery(): Builder
    {
        return static::scopeToUser(parent::getEloquentQuery());
    }

    public static function scopeToUser(Builder $query, ?User $user = null): Builder
    {
        /** @var User|null $user */
        $user ??= Auth::user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->hasRole('super_admin')) {
            return $query;
        }

        return $query->whereIn('companies.id', $user->companies()->select('companies.id'));
    }

    public static function form(Schema $schema): Schema
    {
        return CompanyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompaniesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompanies::route('/'),
            'create' => CreateCompany::route('/create'),
            'edit' => EditCompany::route('/{record}/edit'),
        ];
    }
}
