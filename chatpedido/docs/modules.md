# Módulos

## Catálogo global vs. feature flags por empresa

Dos conceptos distintos que conviene no confundir:

- **Catálogo global** (`modules` / `App\Core\Modules\Models\Module`): qué módulos existen instalados en el sistema. Se puebla por **auto-discovery**, nunca a mano.
- **Feature flags por empresa** (`company_modules` / `App\Core\Features\Models\CompanyModule`): de esos módulos instalados, cuáles tiene **habilitados** una empresa concreta. Ver [features.md](features.md).

## Auto-discovery: `module.json`

Cada módulo vive en una carpeta dentro de `Modules/` (raíz del proyecto Laravel, hermana de `app/`), con un manifiesto `module.json`:

```
Modules/
└── Billing/
    ├── module.json
    └── ... (código del módulo)
```

```json
{
    "name": "Billing",
    "slug": "billing",
    "version": "1.0",
    "description": "Facturación electrónica",
    "provider": "Modules\\Billing\\BillingServiceProvider",
    "dependencies": []
}
```

`ModuleRegistry::discover()` escanea `Modules/` buscando `module.json`; `ModuleRegistry::sync()` hace upsert de lo encontrado en la tabla `modules` (por `slug`). Nunca se registra un módulo manualmente en código.

## Comandos artisan

| Comando | Qué hace |
|---|---|
| `starter:module-discover` | Escanea `Modules/` y sincroniza el catálogo |
| `starter:modules` | Lista los módulos del catálogo y su estado |
| `starter:module-install {slug}` | Sincroniza y habilita el módulo globalmente |
| `starter:module-enable {slug} [--company=slug]` | Habilita el módulo (globalmente, o para una empresa si se pasa `--company`) |
| `starter:module-disable {slug} [--company=slug]` | Análogo, deshabilita |
| `starter:module-remove {slug}` | Elimina el módulo del catálogo (y sus feature flags) |

Todos usan `App\Core\Modules\ModuleManager` internamente — no dupliques esta lógica en otros comandos.

## Carga del Service Provider de un módulo

Si el `module.json` declara un `provider` y el módulo está habilitado globalmente (`modules.enabled = true`), `ModuleLoader::loadEnabled()` lo registra automáticamente en el contenedor en cada arranque de la aplicación (`CoreServiceProvider::boot()`). Si la clase no existe, se registra un warning en el log en vez de romper el arranque — imprescindible para no tumbar la aplicación si un módulo se desinstaló del filesystem pero sigue en la BD.

## Panel Filament

`/admin/modules` (solo lectura + toggle de habilitado/deshabilitado global — los módulos se instalan vía discovery/comandos, no se "crean" a mano desde el panel). `/admin/company-modules` gestiona los feature flags por empresa (alta, edición, baja).
