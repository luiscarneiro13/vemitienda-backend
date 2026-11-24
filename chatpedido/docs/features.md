# Feature Flags

`App\Core\Features\FeatureManager` controla qué módulos del catálogo global (ver [modules.md](modules.md)) tiene habilitados una empresa concreta, vía la tabla pivote `company_modules`.

## API

```php
use App\Core\Features\FeatureManager;

$features = app(FeatureManager::class);

$features->enable($company, 'billing');
$features->disable($company, 'billing');
$features->enabled($company, 'billing');   // bool
$features->disabled($company, 'billing');  // bool
$features->sync($company, ['billing', 'inventory']); // habilita solo estos, deshabilita el resto
$features->isInstalled('billing');         // existe en el catálogo global (independiente de si está habilitado para alguna empresa)
```

## Helper `feature()`

Para comprobar el módulo contra la **empresa activa** (`tenant()->company()`) sin pasarla explícitamente:

```php
if (feature('billing')) {
    // el módulo está habilitado para la empresa del usuario autenticado
}
```

`feature()` sin argumentos devuelve el `FeatureManager` para encadenar cualquier otro método.

## Middleware `module.enabled`

Para proteger rutas completas de un módulo:

```php
Route::middleware('module.enabled:billing')->group(function () {
    // rutas del módulo de facturación
});
```

Devuelve 403 si no hay empresa activa o si el módulo no está habilitado para ella.

## Eventos

`FeatureManager::enable()`/`disable()` disparan `App\Core\Features\Events\FeatureEnabled` / `FeatureDisabled` (con la `Company` y el `Module` involucrados) — engánchate a estos eventos en vez de envolver `FeatureManager` si necesitas reaccionar a un cambio de feature flag (p. ej. enviar una notificación de bienvenida al activar un módulo).
