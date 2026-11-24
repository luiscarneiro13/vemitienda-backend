# Laravel Starter

Plantilla privada y reutilizable para arrancar rápidamente nuevos sistemas administrativos, ERP, CRM y aplicaciones SaaS sobre **Laravel 12**, con autenticación, panel administrativo, roles y permisos, auditoría y multitenancy ya resueltos.

Cada nuevo proyecto puede comenzar clonando este repositorio.

## Stack

- Laravel 12 · PHP 8.3 · MySQL 8
- [Laravel Breeze](https://laravel.com/docs/starter-kits#breeze) (stack Blade) — login, registro, verificación de email, perfil
- [FilamentPHP v4](https://filamentphp.com) — panel administrativo en `/admin`
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission) — roles y permisos
- [Filament Shield](https://github.com/bezhanSalleh/filament-shield) — generación automática de permisos por Resource/Page
- [Laravel Sanctum](https://laravel.com/docs/sanctum) — API tokens
- [Laravel Auditing](https://laravel-auditing.com) — auditoría de modelos y eventos de login/logout
- Todo corre en Docker: no requiere PHP/Composer/Node instalados localmente

## Estructura del repositorio

```
delivery/
├── .github/                 # CI (lint + tests)
├── docs/                    # Documentación detallada
├── nginx/
│   └── default.conf
├── services/
│   └── laravel/             # Aplicación Laravel completa
│       ├── Dockerfile        # multi-stage: base, dev, node_builder, prod
│       ├── app/
│       ├── bootstrap/
│       ├── config/
│       ├── database/
│       ├── public/
│       ├── resources/
│       ├── routes/
│       ├── storage/
│       └── artisan
├── docker-compose.yml        # entorno de desarrollo
├── docker-compose.prod.yml   # entorno de producción
├── .env / .env.example       # variables de infraestructura (Docker Compose)
└── README.md
```

## Instalación (Docker)

Requisitos: Docker y Docker Compose. Nada más.

```bash
# 1. Variables de infraestructura (Docker Compose)
cp .env.example .env

# 2. Variables de la aplicación Laravel
cp services/laravel/.env.example services/laravel/.env

# 3. Levantar todos los servicios
docker compose up -d

# 4. Migrar y sembrar datos iniciales
docker compose exec delivery-app php artisan migrate --seed

# 5. Compilar assets (Tailwind/Vite) — solo la primera vez
docker compose exec delivery-app npm install
docker compose exec delivery-app npm run build
```

La aplicación queda disponible en `http://localhost:8000` y el panel de administración en `http://localhost:8000/admin`.

**Usuario administrador inicial** (creado por el seeder):

| Email | Contraseña |
|---|---|
| `admin@example.com` | `password` |

Ver [docs/installation.md](docs/installation.md) para el detalle paso a paso y [docs/docker.md](docs/docker.md) para la arquitectura de contenedores y variables de entorno.

## Migraciones y seeders

```bash
docker compose exec delivery-app php artisan migrate --seed
```

Esto crea, en orden: permisos de Shield (`shield:generate --all`), permisos de negocio (`recurso.accion`), roles (`super_admin`, `admin`, `manager`, `user`), una empresa demo, el usuario administrador, configuración inicial y planes de ejemplo.

## Crear un módulo nuevo

Patrón recomendado (Model → Migration → Service → Policy → Filament Resource):

```bash
docker compose exec delivery-app php artisan make:model Modulo -m
docker compose exec delivery-app php artisan make:filament-resource Modulo --generate
```

1. Define la migración y el modelo en `app/Models`.
2. Si el modelo debe estar aislado por empresa, añade el trait `App\Models\Concerns\BelongsToCompany`.
3. Crea un `App\Services\ModuloService` con la lógica de negocio — nunca la pongas en el Controller o en el Filament Resource.
4. Ejecuta `php artisan shield:generate --all --panel=admin` para que Shield genere los permisos del nuevo Resource.
5. Añade los permisos/policy necesarios y asígnalos a los roles en `database/seeders/RoleSeeder.php`.

Más detalle en [docs/architecture.md](docs/architecture.md).

## Filament, Shield y permisos

- El panel vive en `App\Providers\Filament\AdminPanelProvider` (`/admin`), con auto-discovery de Resources, Pages y Widgets.
- Filament Shield genera automáticamente permisos `Accion:Modelo` (p. ej. `View:User`, `Create:Company`) para cada Resource/Page. Regenéralos con:
  ```bash
  docker compose exec delivery-app php artisan shield:generate --all --panel=admin
  ```
- Además existen permisos de negocio con formato `recurso.accion` (`users.view`, `companies.create`, etc.), pensados para autorizar fuera del panel (Blade, API, Form Requests).
- `super_admin` tiene bypass total vía `Gate::before` (configurado por Shield) y además todos los permisos asignados explícitamente.

Detalle completo de la matriz de roles/permisos en [docs/permissions.md](docs/permissions.md).

## Multitenancy

Arquitectura single-database: cada usuario pertenece a una o varias empresas (`company_user`) y tiene una `current_company_id` activa. El trait `BelongsToCompany` + `CompanyScope` (Global Scope) filtran automáticamente cualquier modelo que lo use por la empresa activa del usuario autenticado, con bypass para `super_admin`. Ver [docs/architecture.md](docs/architecture.md).

## Documentación

- [docs/installation.md](docs/installation.md) — instalación paso a paso
- [docs/docker.md](docs/docker.md) — servicios, `.env`, Dockerfile multi-stage
- [docs/architecture.md](docs/architecture.md) — capas, multitenancy, Service Layer
- [docs/permissions.md](docs/permissions.md) — roles, permisos, Shield

## Licencia

Uso privado.
