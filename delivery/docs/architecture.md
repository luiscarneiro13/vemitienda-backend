# Arquitectura

> Para la infraestructura reutilizable sobre la que viven los módulos de negocio (multitenancy, módulos, feature flags, settings, media, notificaciones, timeline), ver [core.md](core.md) y los documentos enlazados desde ahí. Este documento cubre las capas generales de la aplicación y cómo el Core encaja en ellas.

## Capas

Siguiendo Laravel best practices + SOLID:

- **Controllers / Filament Resources**: reciben la petición, delegan y devuelven respuesta. Sin lógica de negocio.
- **Form Requests** (`app/Http/Requests`): validación y autorización de entrada (fuera del panel Filament; dentro del panel, Filament valida vía el `Schema` del Resource).
- **Services**: lógica de negocio reutilizable. Los del Core viven junto a su engine (`App\Core\Companies\Services\CompanyService`, `App\Core\Branches\Services\BranchService`); los del Starter que no pertenecen al Core siguen en `app/Services` (`SettingsService` fue reemplazado por `App\Core\Settings\SettingsManager`, ver [settings.md](settings.md); `SubscriptionService`, `CompanyService`(API) permanecen en `app/Services`).
- **Policies** (`app/Policies` para modelos fuera de `app/Core`; junto al modelo dentro de `app/Core/*/Policies` para los del Core): autorización a nivel de modelo, generadas automáticamente por Filament Shield para los modelos con Resource, e integradas con los permisos `recurso.accion` para los modelos sin Resource propio.
- **Models**: relaciones, scopes, casts. Sin lógica de aplicación.

Los Filament Resources interactúan con Eloquent directamente (patrón estándar del ecosistema Filament) — no se introduce una capa de Service intermedia ahí salvo que haya lógica de negocio real que no sea simple persistencia.

## El Core Framework

`app/Core` (ver [core.md](core.md)) es la infraestructura reutilizable de la que dependen todos los módulos de negocio: `Company`/`Branch` (los tenants), `TenantContext`/`TenantResolver` (multitenancy), `Modules`/`Features` (catálogo de módulos y feature flags por empresa), `Settings` (configuración tipada), `Media` (archivos), `Notifications` (envío centralizado) y `Timeline` (actividad funcional).

Regla de ubicación: las piezas que Laravel/Filament auto-descubren por convención de ruta (migraciones, factories, seeders, Policies, Filament Resources) se quedan en sus ubicaciones estándar del framework — solo la lógica de dominio pura vive dentro de `app/Core`.

## Multitenancy (single-database)

Decisión: una sola base de datos, aislamiento por columna `company_id` + Global Scope. Es el approach más simple de adoptar para una plantilla que se clona hacia proyectos nuevos (ERP/CRM/SaaS), sin la complejidad operativa de bases de datos por tenant.

Detalle completo en [tenancy.md](tenancy.md). Resumen:

- **`Company`** (`App\Core\Companies\Models\Company`): el tenant. Incluye `uuid`, `legal_name`, `country`, `timezone`, `language`, `currency`, `logo`, `status`, auditoría de quién crea/modifica/borra (`created_by`/`updated_by`/`deleted_by`) y soft deletes.
- **`Branch`** (`App\Core\Branches\Models\Branch`): sucursales de una empresa (`company.branches()`).
- **`company_user`**: membresías N:M entre usuarios y empresas.
- **`users.company_id`** / **`users.current_company_id`** / **`users.current_branch_id`**: empresa "principal", empresa activa y sucursal activa del usuario.
- **`App\Core\Scopes\CompanyScope`** + **`App\Core\Traits\HasCompany`**: Global Scope + trait, aplicados automáticamente por `App\Core\Support\TenantModel`. Bypass para `super_admin`.
- **`App\Core\Tenancy\TenantContext`**: resuelve la empresa/sucursal/usuario activos vía `TenantResolverManager` (Strategy Pattern) — nunca se consulta `Auth::user()` directamente desde un módulo.

### Añadir un modelo nuevo al multitenancy

```php
use App\Core\Support\TenantModel;

class Invoice extends TenantModel
{
    // Invoice::all() ya filtra automáticamente por la empresa activa.
}
```

## Auditoría vs. Timeline

- **Laravel Auditing** (`owen-it/laravel-auditing`) registra automáticamente creación/actualización/eliminación en los modelos `Auditable` (`User`, `Company`) — cambios crudos de columnas.
- **Timeline** (`App\Core\Timeline`, ver [timeline.md](timeline.md)) registra actividad funcional legible ("Luis creó un cliente") — no reemplaza a Auditing, lo complementa.
- **Login/Logout** se registran manualmente vía listeners (`App\Listeners\LogSuccessfulLogin`/`LogSuccessfulLogout`), porque no son eventos de ciclo de vida de un modelo Eloquent.
- El Filament Resource `AuditResource` es de solo lectura y muestra usuario, acción, modelo, fecha, IP y un modal con valores anteriores/nuevos. `TimelineEntryResource` es su equivalente para la actividad funcional.

## Billing scaffold

`Plan` y `Subscription` (`app/Models`) son un scaffold básico (sin integración de pasarela de pago). `Subscription` usa el trait `App\Core\Traits\HasCompany` directamente (no necesita ser un `TenantModel` completo). `SubscriptionService` centraliza alta/cancelación. Para integrar Stripe/Paddle en un proyecto derivado, esta es la capa a extender.
