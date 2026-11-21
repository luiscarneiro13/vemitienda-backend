<?php

namespace App\Providers;

use App\Core\Branches\Models\Branch;
use App\Core\Branches\Policies\BranchPolicy;
use App\Core\Companies\Events\CompanyCreated;
use App\Core\Companies\Models\Company;
use App\Domain\Catalog\Listeners\CreateDefaultCatalogForCompany;
use App\Domain\Catalog\Models\Catalog;
use App\Domain\Catalog\Models\CatalogCategory;
use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\ProductOption;
use App\Domain\Catalog\Models\ProductOptionGroup;
use App\Domain\Catalog\Policies\CatalogCategoryPolicy;
use App\Domain\Catalog\Policies\CatalogPolicy;
use App\Domain\Catalog\Policies\ProductPolicy;
use App\Domain\Catalog\Services\PublicCatalogCache;
use App\Domain\Locations\Models\City;
use App\Domain\Locations\Policies\CityPolicy;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogSuccessfulLogout;
use App\Policies\AuditPolicy;
use App\Policies\CompanyPolicy;
use App\Policies\DatabaseNotificationPolicy;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use Dedoc\Scramble\Scramble;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use OwenIt\Auditing\Models\Audit;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (class_exists(Scramble::class)) {
            Scramble::ignoreDefaultRoutes();
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('viewApiDocs', fn ($user): bool => (bool) $user?->hasRole('super_admin'));

        Event::listen(Login::class, LogSuccessfulLogin::class);
        Event::listen(Logout::class, LogSuccessfulLogout::class);

        // Modelos fuera de app/Models: Shield no los detecta por convención,
        // así que se registran explícitamente. Company vive en app/Core, por
        // lo que el auto-discovery de policies de Laravel tampoco la
        // encontraría sin este registro explícito.
        Gate::policy(Audit::class, AuditPolicy::class);
        Gate::policy(DatabaseNotification::class, DatabaseNotificationPolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Company::class, CompanyPolicy::class);
        Gate::policy(City::class, CityPolicy::class);
        Gate::policy(Branch::class, BranchPolicy::class);
        Gate::policy(Catalog::class, CatalogPolicy::class);
        Gate::policy(CatalogCategory::class, CatalogCategoryPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);

        Event::listen(CompanyCreated::class, CreateDefaultCatalogForCompany::class);

        $this->registerPublicCatalogCacheInvalidation();

        RateLimiter::for('public-catalog', function (Request $request) {
            $limit = (int) config('catalog.rate_limit_per_minute', app()->environment('testing') ? 60 : 600);

            return Limit::perMinute($limit)->by($request->ip());
        });

        RateLimiter::for('public-catalog-write', function (Request $request) {
            $limit = (int) config('catalog.write_rate_limit_per_minute', 60);

            return Limit::perMinute($limit)->by($request->ip());
        });
    }

    private function registerPublicCatalogCacheInvalidation(): void
    {
        foreach ([Company::class, Branch::class, Catalog::class] as $directoryAffectingModel) {
            $directoryAffectingModel::saved(function ($model): void {
                $companyId = $model instanceof Company ? (int) $model->id : (int) $model->company_id;
                PublicCatalogCache::bumpCompany($companyId, true);
            });
            $directoryAffectingModel::deleted(function ($model): void {
                $companyId = $model instanceof Company ? (int) $model->id : (int) $model->company_id;
                PublicCatalogCache::bumpCompany($companyId, true);
            });
        }

        foreach ([CatalogCategory::class, Product::class, ProductOptionGroup::class, ProductOption::class] as $catalogModel) {
            $catalogModel::saved(fn ($model) => PublicCatalogCache::bumpCompany((int) $model->company_id));
            $catalogModel::deleted(fn ($model) => PublicCatalogCache::bumpCompany((int) $model->company_id));
        }

        City::saved(fn () => PublicCatalogCache::bumpDirectory());
        City::deleted(fn () => PublicCatalogCache::bumpDirectory());
    }
}
