<?php

namespace App\Core\Providers;

use App\Core\Branches\Models\Branch;
use App\Core\Branches\Observers\BranchObserver;
use App\Core\Companies\Models\Company;
use App\Core\Companies\Observers\CompanyObserver;
use App\Core\Media\Contracts\MediaDriverInterface;
use App\Core\Media\Drivers\LocalMediaDriver;
use App\Core\Modules\ModuleLoader;
use App\Core\Tenancy\Resolvers\AuthenticatedUserTenantResolver;
use App\Core\Tenancy\Resolvers\TenantResolverManager;
use App\Core\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\ServiceProvider;

/**
 * Punto único de registro de toda la infraestructura del Core. Ningún
 * servicio del Core se registra manualmente en AppServiceProvider — todo
 * vive y se resuelve desde aquí.
 */
class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TenantResolverManager::class, function ($app) {
            return (new TenantResolverManager)
                ->register($app->make(AuthenticatedUserTenantResolver::class));
        });

        $this->app->singleton(TenantContext::class);

        $this->app->bind(MediaDriverInterface::class, function () {
            return match (config('core.media.driver')) {
                default => new LocalMediaDriver(config('core.media.disk')),
            };
        });
    }

    public function boot(): void
    {
        Company::observe(CompanyObserver::class);
        Branch::observe(BranchObserver::class);

        $this->app->make(ModuleLoader::class)->loadEnabled();

        // Los modelos del Core viven en App\Core\*\Models, no en App\Models:
        // el resolver de factories por defecto de Laravel solo entiende esa
        // segunda convención. Se resuelve siempre por el nombre corto de la
        // clase (Database\Factories\{ClassBasename}Factory).
        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }
}
