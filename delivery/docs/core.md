# Core Framework

`app/Core` es el framework interno sobre el que vive todo módulo de negocio futuro (CRM, ERP, Inventario, Facturación...). No contiene lógica de negocio — es infraestructura reutilizable: multitenancy, módulos/feature flags, configuración, archivos, notificaciones y auditoría funcional.

## Por qué existe

El Starter (Breeze + Filament + Spatie Permission + Shield + Auditing) ya resuelve autenticación, panel admin, roles y permisos. El Core añade la capa que falta para soportar **decenas de módulos independientes** sobre esa base: cómo cada módulo sabe "en qué empresa/sucursal estoy", cómo se activa/desactiva por cliente, dónde vive la configuración, cómo se registran archivos y cómo se cuenta la actividad del negocio.

## Estructura

```
app/Core/
├── Companies/     Company Engine: modelo, repositorio, servicio, actions, eventos, observer
├── Branches/       Branch Engine: sucursales de una empresa
├── Tenancy/        TenantContext + TenantResolver (Strategy Pattern)
├── Modules/        Catálogo global de módulos (auto-discovery vía module.json)
├── Features/       Feature flags: qué módulos tiene habilitados cada empresa
├── Settings/       Motor de configuración (grupos, tipos, cache)
├── Media/          Gestión de archivos con driver intercambiable
├── Notifications/  Punto único de envío de notificaciones
├── Timeline/        Actividad funcional legible ("Luis creó un cliente")
├── Traits/          HasCompany, HasBranch, HasCreatedBy, HasUpdatedBy, HasDeletedBy, HasStatus, HasUuid, HasSlug
├── Scopes/          CompanyScope (Global Scope de multitenancy)
├── Support/         CoreModel, TenantModel, CompanyModel, BranchModel (clases base)
├── Middleware/      ResolveTenant, EnsureCompanyIsActive, EnsureModuleEnabled, EnsureBranchSelected
├── Helpers/         Funciones globales (tenant(), company(), setting(), module()...)
└── Providers/       CoreServiceProvider — único punto de registro de todo lo anterior
```

Las piezas que Laravel/Filament escanean por convención de ubicación (migraciones, factories, seeders, Policies, Filament Resources) se quedan en sus rutas estándar (`database/migrations`, `app/Policies`, `app/Filament/Resources`) — referencian los modelos del Core, pero no viven dentro de `app/Core`. Esto es deliberado: esas ubicaciones son parte del "contrato" con el framework, no lógica del Core en sí.

## Convención de cada Engine (Companies, Branches, y los que sigan)

```
Nombre/
├── Models/         Eloquent model
├── Repositories/    Acceso a datos (queries), sin lógica de negocio
├── Services/        Orquesta Repository + Actions, API pública del engine
├── Actions/         Una clase por operación de negocio (Create, Update, Activate...)
├── Events/          Eventos de dominio (CompanyCreated, CompanyDeleted...)
└── Observers/       Escucha el ciclo de vida Eloquent, dispara Events + Timeline
```

`Service` es lo que el resto de la aplicación debe usar (Filament Resources, Controllers, otros módulos) — nunca instancies `Repository` o `Action` directamente desde fuera del engine.

## Cómo construir un módulo de negocio nuevo

1. **Migración** en `database/migrations` con `company_id` (y `branch_id` si aplica).
2. **Modelo** extendiendo `App\Core\Support\TenantModel` (o `BranchModel` si además pertenece a una sucursal) — esto aplica `HasCompany` automáticamente, sin que tengas que escribir `where('company_id', ...)` en ningún sitio.
3. **Service** en `app/Services` (o dentro del propio módulo si vive en `Modules/`) que use `tenant()`/`company()` cuando necesite el contexto actual, nunca `Auth::user()` directamente.
4. **Policy** — la genera Filament Shield automáticamente al crear el Resource (`php artisan shield:generate --all --panel=admin`), siempre que el modelo siga la convención `Models/` → `Policies/` en su namespace.
5. **Filament Resource** en `app/Filament/Resources` para exponerlo en `/admin`.
6. Si el módulo debe poder activarse/desactivarse por empresa: añade un `module.json` en `Modules/TuModulo/` (ver [modules.md](modules.md)) y comprueba `feature('tu-modulo')` donde corresponda.
7. Registra eventos de dominio relevantes y, si tiene sentido, un `Observer` que llame a `timeline()->log(...)` para dejar rastro legible de la actividad.

Ver [tenancy.md](tenancy.md), [modules.md](modules.md), [features.md](features.md), [settings.md](settings.md), [media.md](media.md) y [timeline.md](timeline.md) para el detalle de cada pieza.
