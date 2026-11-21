# Tenancy

## TenantContext: la única fuente de verdad

`App\Core\Tenancy\TenantContext` es el único lugar del sistema que debe resolver "quién es la empresa/sucursal/usuario actuales". Ningún módulo debe llamar a `Auth::user()` directamente para eso.

```php
tenant()->company();      // App\Core\Companies\Models\Company|null
tenant()->branch();       // App\Core\Branches\Models\Branch|null
tenant()->user();         // App\Models\User|null
tenant()->companyId();    // int|null
tenant()->branchId();     // int|null
tenant()->userId();       // int|null
```

Atajos: `company()` y `branch()` (funciones globales) son equivalentes a `tenant()->company()` / `tenant()->branch()`.

### Cambiar de empresa/sucursal activa

```php
tenant()->switchTo($user, $company);       // valida que $user pertenezca a $company (403 si no)
tenant()->switchBranchTo($user, $branch);  // valida que $branch pertenezca a la empresa activa del usuario
```

### Caché por request

`TenantContext` cachea la empresa resuelta durante la vida de la request, pero invalida esa caché automáticamente si el usuario autenticado cambia (login/logout a mitad de proceso, o resolución temprana antes de que el usuario esté autenticado). No necesitas preocuparte por refrescarla manualmente salvo tras un `switchTo()` (que ya lo hace por ti) — `forgetResolvedCompany()` está disponible si alguna vez lo necesitas explícitamente (p. ej. en tests).

## TenantResolver: Strategy Pattern

`TenantContext` no resuelve la empresa por sí mismo — delega en `App\Core\Tenancy\Resolvers\TenantResolverManager`, que prueba una lista de estrategias (`TenantResolverInterface`) en orden hasta que una devuelva una empresa.

Hoy solo existe una estrategia registrada, `AuthenticatedUserTenantResolver` (resuelve desde `current_company_id` del usuario autenticado), registrada en `CoreServiceProvider`:

```php
$this->app->singleton(TenantResolverManager::class, function ($app) {
    return (new TenantResolverManager)
        ->register($app->make(AuthenticatedUserTenantResolver::class));
});
```

### Añadir una estrategia nueva (subdominio, dominio propio, header, API key...)

1. Crea una clase que implemente `TenantResolverInterface`:

```php
class SubdomainTenantResolver implements TenantResolverInterface
{
    public function key(): string { return 'subdomain'; }

    public function resolve(): ?Company
    {
        $subdomain = explode('.', request()->getHost())[0] ?? null;

        return $subdomain ? Company::where('slug', $subdomain)->first() : null;
    }
}
```

2. Regístrala en `CoreServiceProvider` (el orden de `register()` es el orden de intento):

```php
->register($app->make(AuthenticatedUserTenantResolver::class))
->register($app->make(SubdomainTenantResolver::class));
```

No se modifica nada más: `TenantContext`, los traits `HasCompany`/`HasBranch` y el resto del Core siguen funcionando igual, ya que solo dependen de `TenantResolverManager::resolve()`.

## Multitenancy: HasCompany / HasBranch

- **`HasCompany`** (usado por `TenantModel`): añade el `CompanyScope` (Global Scope) que filtra automáticamente por `company_id = tenant()->companyId()`, y autoasigna `company_id` al crear un registro si no viene seteado. `super_admin` bypassea el scope. Usa `Modelo::withoutTenancy()` para consultas explícitamente globales.
- **`HasBranch`** (usado por `BranchModel`): relaciona el modelo con una sucursal y autoasigna `branch_id`, sin imponer un scope global obligatorio (no todo recurso de una empresa está necesariamente acotado a una sucursal concreta).

```php
use App\Core\Support\TenantModel;

class Invoice extends TenantModel
{
    // Invoice::all() ya filtra automáticamente por la empresa activa.
}
```

```php
use App\Core\Support\BranchModel;

class StockMovement extends BranchModel
{
    // Además de company_id, autoasigna branch_id.
}
```
