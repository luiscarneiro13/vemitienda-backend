# Roles y permisos

Conviven **dos** sistemas de permisos, ambos sobre Spatie Laravel Permission:

## 1. Permisos generados por Filament Shield

Formato `Accion:Modelo` (p. ej. `View:User`, `Create:Company`, `Delete:Plan`), generados automáticamente por cada Filament Resource/Page/Widget mediante:

```bash
docker compose exec delivery-app php artisan shield:generate --all --panel=admin
```

Estos permisos controlan qué puede hacer un usuario **dentro del panel `/admin`** (ver un Resource, crear un registro, etc.). Se regeneran cada vez que agregas o modificas un Resource — ejecuta el comando de nuevo tras crear uno nuevo.

Importante: estos permisos viven **en la base de datos**, no en una migración. Por eso `database/seeders/DatabaseSeeder.php` ejecuta `shield:generate --all` (vía `Artisan::call`) antes de sembrar roles, para que `migrate:fresh --seed` sea siempre reproducible.

**Modelos del Core** (`app/Core/*/Models`): Shield genera sus Policies como carpeta hermana `Policies/` dentro del mismo namespace del modelo (p. ej. `App\Core\Branches\Models\Branch` → `App\Core\Branches\Policies\BranchPolicy`), y Laravel las auto-descubre sin registro manual — mismo mecanismo que para modelos en `app/Models`. Solo los modelos que viven en namespaces de paquetes externos (Spatie `Role`/`Permission`, `OwenIt\Auditing\Models\Audit`, `Illuminate\Notifications\DatabaseNotification`) necesitan `Gate::policy()` explícito en `AppServiceProvider`.

## 2. Permisos de negocio (`recurso.accion`)

Definidos explícitamente en `database/seeders/PermissionSeeder.php`:

```
users.view        users.create        users.update        users.delete
roles.view         roles.create        roles.update        roles.delete
permissions.view   permissions.create  permissions.update  permissions.delete
companies.view     companies.create    companies.update    companies.delete
settings.view      settings.update
```

Pensados para autorizar **fuera** del panel Filament (controladores Blade, API, Form Requests), donde no tiene sentido depender de la existencia de un Resource concreto. Ejemplo de uso en un Form Request:

```php
public function authorize(): bool
{
    return $this->user()?->can('companies.create') ?? false;
}
```

## Roles iniciales

| Rol (slug Spatie) | Descripción |
|---|---|
| `super_admin` | Acceso total. Tiene **todos** los permisos asignados explícitamente y además bypass vía `Gate::before` (configurado por Shield en `config/filament-shield.php`, doble seguro). |
| `admin` | Gestión completa de usuarios/empresas/configuración; solo lectura sobre roles y permisos. |
| `manager` | Gestión de usuarios y visibilidad de configuración, acotado a su empresa (vía el scope de multitenancy). |
| `user` | Sin acceso al panel `/admin`; solo su perfil y, si aplica, la API. |

## Matriz de permisos de negocio por rol

| Permiso | super_admin | admin | manager | user |
|---|---|---|---|---|
| `users.view / create / update` | ✔ | ✔ | ✔ (su empresa, vía scope) | ✘ |
| `users.delete` | ✔ | ✔ | ✘ | ✘ |
| `roles.view` | ✔ | ✔ | ✘ | ✘ |
| `roles.create / update / delete` | ✔ | ✘ | ✘ | ✘ |
| `permissions.view` | ✔ | ✔ | ✘ | ✘ |
| `permissions.create / update / delete` | ✔ | ✘ | ✘ | ✘ |
| `companies.view` | ✔ (todas) | ✔ (todas) | ✔ (la propia) | ✘ |
| `companies.create / update / delete` | ✔ | ✔ | ✘ | ✘ |
| `settings.view / update` | ✔ | ✔ | view únicamente | ✘ |
| Acceso a `/admin` | ✔ | ✔ | ✔ | ✘ |

La asignación exacta de permisos Shield (`Accion:Modelo`) por rol vive en `database/seeders/RoleSeeder.php`, constante `SHIELD_PERMISSION_SUFFIXES`.

## Modificar la matriz

1. Edita `PermissionSeeder` (nuevos permisos de negocio) y/o crea el Resource correspondiente + `shield:generate --all`.
2. Ajusta las constantes `BUSINESS_PERMISSIONS` / `SHIELD_PERMISSION_SUFFIXES` en `RoleSeeder`.
3. `php artisan migrate:fresh --seed` (desarrollo) para verificar, o crea un seeder de actualización idempotente para producción (los seeders existentes usan `findOrCreate`/`syncPermissions`, son seguros de re-ejecutar).
