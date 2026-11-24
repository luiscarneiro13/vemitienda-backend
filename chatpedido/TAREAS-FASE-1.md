# TAREAS — Fase 1: Catálogo web + pedido por WhatsApp (capa gratuita)

> **Lee esto completo antes de empezar.** Este archivo es la guía de implementación paso a paso.
> Las reglas de negocio están en `AGENTS.md` (raíz). Si algo de este archivo contradice `AGENTS.md`, manda `AGENTS.md` y debes avisar al propietario.

---

## 0. Cómo usar este archivo

- Cada tarea tiene una casilla `- [ ]`. Cuando la termines **y la verifiques**, cámbiala a `- [x]`.
- Hay dos casillas por bloque cuando aplica: **Implementado** y **Probado**. No marques "Probado" si los tests no pasan.
- Haz las fases **en orden**. No empieces una fase si la anterior tiene tests en rojo.
- Al terminar cada fase ejecuta la sección "Verificación de la fase".
- Todo lo necesario está especificado y decidido en este archivo. Sigue las instrucciones **literalmente**: no cambies nombres, rutas, columnas ni textos. Si aun así encuentras algo que no esté cubierto, **detente** y pregunta al propietario antes de seguir; no inventes reglas de negocio.
- No borres ni renombres columnas/tablas existentes. No modifiques migraciones ya existentes: **siempre crea migraciones nuevas**.
- No agregues dependencias nuevas (composer/npm) sin aprobación. Todo lo de esta fase se hace con lo que ya está instalado (Laravel 12, Filament 4, Alpine.js, Tailwind 3, Spatie Permission, Filament Shield).
- Idioma: textos de UI en **español**. Nombres de clases, columnas y código en **inglés**.
- Commits: uno al terminar cada fase, Conventional Commits en inglés (`feat:`, `fix:`, `chore:`, `docs:`, `test:`). Ejemplo: `feat: add cities management (phase 1)`.

### 0.1 Contexto técnico obligatorio

| Concepto | Valor |
|---|---|
| Código Laravel | `services/laravel/` (todas las rutas de este archivo son relativas a esa carpeta salvo que se diga lo contrario) |
| Contenedor app | `delivery-app` (Docker Compose desde la raíz del repo) |
| URL local | `http://localhost:8000` — panel en `/admin` |
| Tests | PHPUnit 11 (clases, **no Pest**), SQLite en memoria (`phpunit.xml`) |
| Tienda | modelo `App\Core\Companies\Models\Company` (tabla `companies`) |
| Sucursal | modelo `App\Core\Branches\Models\Branch` (tabla `branches`) |
| Aislamiento por tienda | `App\Core\Support\TenantModel` → trait `HasCompany` → `CompanyScope` |

### 0.2 Comandos (ejecutar desde la raíz del repo: `/home/desarrollo/Documentos/PROYECTOS/Delivery/delivery`)

```bash
docker compose up -d                                                   # levantar
docker compose exec delivery-app php artisan migrate                  # migrar
docker compose exec delivery-app php artisan test                     # todos los tests
docker compose exec delivery-app php artisan test --filter=NombreTest # un test
docker compose exec delivery-app composer dump-autoload
docker compose exec delivery-app npm run build                        # compilar JS/CSS
docker compose exec delivery-app vendor/bin/pint app database routes tests   # formato
docker compose exec delivery-app php artisan route:list --path=c      # ver rutas públicas
docker compose exec delivery-app php artisan storage:link             # enlace de imágenes públicas
```

> ⚠️ **NUNCA** ejecutes `php artisan migrate:fresh`, `db:wipe` o similares contra la base de datos de desarrollo sin permiso explícito del propietario (borran datos). Los tests usan SQLite en memoria, no necesitan eso.

> ⚠️ **NUNCA** ejecutes `php artisan shield:generate` sin la opción `--ignore-existing-policies`: sobrescribe las policies personalizadas.

### 0.3 Reglas técnicas obligatorias (comportamiento real del proyecto y cómo se resuelve)

Cada regla describe cómo funciona el proyecto **hoy** y la solución exacta que ya está incluida en las tareas. No hay que investigar nada: solo aplicar.

| # | Cómo funciona el proyecto hoy | Regla que debes aplicar | Dónde está resuelto |
|---|---|---|---|
| R1 | `CompanyScope` (`app/Core/Scopes/CompanyScope.php`): sin usuario logueado **no filtra**; con `super_admin` **no filtra**; con cualquier otro usuario filtra por su `current_company_id`. Si el dueño de la tienda A abre el enlace de la tienda B, sin esta regla vería el catálogo de B vacío. | **Toda** consulta del catálogo público usa `->withoutTenancy()` y filtra explícitamente por `company_id` o `catalog_id`. En eager loading se aplica dentro del closure: `->with(['products' => fn ($q) => $q->withoutTenancy()->...])`. | 4.4, 7.3, 7.4 |
| R2 | `HasCompany` pone en `company_id` la empresa activa del usuario logueado al crear. Si el `super_admin` crea un producto de otra tienda, quedaría con la empresa del super_admin. | Los modelos hijos (categoría, producto, grupo, opción) **calculan `company_id` desde su padre** en el evento `saving` de `booted()`, sobrescribiendo siempre lo que puso `HasCompany`. `Catalog` recibe `company_id` explícito siempre. | 3.3, 4.1 |
| R3 | Filament crea las tiendas con Eloquent directo; **no** usa `CreateCompanyAction`. `CompanyObserver::created` dispara el evento `CompanyCreated` (propiedad `public readonly Company $company`). | El catálogo por defecto se crea en un **listener síncrono** de `CompanyCreated`. No se toca `CreateCompanyAction`. | 4.2 |
| R4 | Las policies de modelos fuera de `app/Models` no se autodetectan de forma fiable. | Toda policy nueva se registra con `Gate::policy(Modelo::class, ModeloPolicy::class)` en `AppServiceProvider::boot()`. El registro explícito tiene prioridad sobre cualquier policy autogenerada. | 1.4, 5.3 |
| R5 | `DatabaseSeeder` ejecuta `shield:generate --all`, que **sobrescribe** policies existentes. Shield guarda la policy de un modelo `app/X/Models/Y.php` en `app/X/Policies/YPolicy.php`. | Agregar `--ignore-existing-policies` al seeder y a cualquier ejecución manual. Las policies nuevas se crean exactamente en la ruta donde Shield las buscaría (`app/Domain/Catalog/Policies/`, `app/Domain/Locations/Policies/`), así Shield las detecta como existentes y no las toca. | 5.1, 6.7 |
| R6 | Los factories se resuelven por nombre corto de la clase (`CoreServiceProvider::boot`). | El modelo `App\Domain\Catalog\Models\Product` usa `Database\Factories\ProductFactory`, y así con todos. | 1.3, 3.4 |
| R7 | Las vistas públicas usan `@vite`; en tests falla si no hay manifest. | En el `setUp()` de todo test que renderice vistas públicas: `$this->withoutVite();`. | 7.10 |
| R8 | El `UserForm` de Filament guarda `company_id` pero **no** crea la fila en `company_user`; sin esa fila el usuario no pertenece a la tienda y no ve nada. | Al crear/editar un usuario en Filament se sincroniza `company_user`. | 5.5 |
| R9 | El proyecto no es un repositorio git. `.gitignore` ya excluye `.env`, `vendor`, `node_modules`, `public/build`, `public/storage`. | Inicializar git antes de empezar. | 0.1 |
| R10 | Al crear una `Company` (también con factory en tests) el listener de R3 crea su catálogo por defecto. | En tests, el catálogo de una tienda se obtiene con `$company->defaultCatalog`. `CatalogFactory` crea catálogos **no** por defecto (`is_default = false`) para no duplicar. | 3.4 |
| R11 | `nginx` sirve `/var/www/public` y monta toda la carpeta `services/laravel`, así que el enlace simbólico `public/storage` funciona sin tocar nginx. | Solo ejecutar `php artisan storage:link`. **No** modificar `nginx/default.conf`. | 7.9 |
| R12 | Versiones instaladas verificadas: Tailwind **3.4.19** (incluye `line-clamp-*` sin plugin), Filament 4 con `TextInput::copyable()`, `FileUpload::imageEditor()`, `Repeater::orderColumn()`/`reorderable()`, `TextColumn::money()`, `ImageColumn::circular()`, `->counts()`, `->visibleOn()`. | Usar esos métodos tal cual. No instalar plugins ni paquetes. | Fases 2, 6, 7 |

---

## Decisiones ya tomadas (no cambiar sin permiso)

| # | Decisión |
|---|---|
| D1 | URL pública: `dominio.com/c/{slug_tienda}/{uuid_sucursal}` |
| D2 | Sin UUID de sucursal, UUID con formato inválido, UUID de otra tienda, sucursal inactiva, tienda inactiva o sucursal sin WhatsApp → **página de error** (HTTP 404), sin fallback a la sucursal principal y **sin revelar cuál fue la causa**. |
| D3 | El pedido se envía al WhatsApp de la **sucursal** del enlace. |
| D4 | Un catálogo por tienda (compartido por sus sucursales). Todo producto/categoría cuelga de `catalog_id`, para poder agregar en el futuro catálogos por sucursal/ciudad **solo agregando tablas** (`catalog_branch`, `catalog_city`) sin tocar las existentes. |
| D5 | Ciudades: catálogo administrado solo por `super_admin`. |
| D6 | Capa gratuita: **no se guardan pedidos** en BD. Solo se genera el texto y se abre WhatsApp. |
| D7 | El dueño de la tienda gestiona su catálogo en el mismo panel Filament (`/admin`). |

## Decisiones de diseño de esta fase (definitivas)

| # | Decisión |
|---|---|
| S1 | Nuevo rol `store_owner` ("Dueño de tienda"). NO puede crear tiendas, ni sucursales, ni ciudades, ni catálogos. Puede ver su tienda, editar datos de sus sucursales (nombre, dirección, teléfono, email, WhatsApp, zona horaria) y gestionar todas sus categorías y productos. El rol `admin` existente **no** recibe permisos del catálogo. |
| S2 | Las sucursales las crea y elimina solo el `super_admin`. |
| S3 | Grupos de opciones (tamaño, extras, salsas...) **por producto** (no compartidos entre productos). |
| S4 | Imagen de producto: una sola, con `FileUpload` de Filament en disco `public`, carpeta `products`, columna `image_path`, máximo 2 MB. |
| S5 | El cliente puede escribir una nota por producto (máx. 140 caracteres) y una nota general (máx. 300). **No** se piden nombre, teléfono ni dirección en la web (eso sigue por WhatsApp). |
| S6 | Una sola moneda: la de la tienda (`companies.currency`, ISO 4217; si está vacía se usa `USD`). Formato con `Intl.NumberFormat('es-VE')`. El mensaje dice "Total estimado". **No** hay doble moneda (USD/Bs) en esta fase. |
| S7 | WhatsApp de la sucursal: se guarda solo con dígitos, con código de país y sin `+` (ej. `584141234567`), entre 10 y 15 dígitos. |
| S8 | **No** se muestra logo de la tienda en esta fase (`logo_url` siempre `null` en el payload). |
| S9 | **No** hay horarios de atención ni bloqueo de pedidos por horario en esta fase. |
| S10 | El catálogo público y el panel usan la zona horaria/idioma por defecto de la app; no se agrega selector de idioma. |

---

## FASE 0 — Preparación

- [x] **0.1** Inicializar git en la raíz del repo (`/home/desarrollo/Documentos/PROYECTOS/Delivery/delivery`), sin modificar `.gitignore`:
  ```bash
  git init
  git add -A
  git status            # verificar que NO aparecen .env, vendor/, node_modules/, public/build/
  git commit -m "chore: initial snapshot before phase 1"
  ```
  Si `git status` muestra un `.env`, **detente** y avisa al propietario antes de hacer commit.
- [x] **0.2** Levantar el entorno: `docker compose up -d`. Abrir `http://localhost:8000/admin` y entrar con `admin@example.com` / `password`.
- [x] **0.3** Ejecutar `docker compose exec delivery-app php artisan test`. **Todos deben pasar antes de empezar** (línea base: 74 tests). Anotar aquí el número: `74` (74 passed, 238 assertions).
- [x] **0.4** Registrar el namespace de módulos (necesario para módulos de pago futuros). En `composer.json`, dentro de `autoload.psr-4`, agregar:
  ```json
  "Modules\\": "Modules/"
  ```
  Luego `docker compose exec delivery-app composer dump-autoload`.
  - [x] Probado: `php artisan test` sigue en verde.
- [x] **0.5** Crear las carpetas nuevas (vacías por ahora):
  - `app/Domain/Catalog/Models`, `app/Domain/Catalog/Policies`, `app/Domain/Catalog/Services`, `app/Domain/Catalog/Actions`, `app/Domain/Catalog/Listeners`
  - `app/Domain/Locations/Models`, `app/Domain/Locations/Policies`
  - `app/Http/Controllers/PublicCatalog`
  - `resources/views/public-catalog`
  - `tests/Feature/Catalog`

> **Por qué `app/Domain` y no `Modules/`:** el catálogo es la funcionalidad base (siempre activa) del producto. Los módulos en `Modules/` solo cargan si están habilitados en la tabla `modules`, lo que en tests (BD vacía) haría desaparecer rutas y modelos. Los módulos de pago futuros (pagos, delivery, pedidos persistidos) sí irán en `Modules/`.

### Verificación Fase 0
- [x] `php artisan test` en verde.

---

## FASE 1 — Ciudades (solo super_admin)

### 1.1 Migración `cities`
- [x] Crear `database/migrations/2026_11_12_000001_create_cities_table.php`:
  ```php
  Schema::create('cities', function (Blueprint $table) {
      $table->id();
      $table->string('name');                 // "El Tigre"
      $table->string('slug')->unique();       // "el-tigre" (lo genera HasSlug desde name)
      $table->string('state')->nullable();    // "Anzoátegui"
      $table->string('country', 2)->default('VE');
      $table->boolean('is_active')->default(true);
      $table->timestamps();
  });
  ```
  `down()`: `Schema::dropIfExists('cities');`

### 1.2 Modelo `City`
- [x] Crear `app/Domain/Locations/Models/City.php`:
  - `namespace App\Domain\Locations\Models;`
  - `class City extends \App\Core\Support\CoreModel` (**NO** `TenantModel`: las ciudades son globales).
  - `use HasFactory, \App\Core\Traits\HasSlug;` (el slug se genera solo desde `name` si viene vacío).
  - `$fillable = ['name', 'slug', 'state', 'country', 'is_active'];`
  - `casts(): ['is_active' => 'boolean']`
  - Relación `branches(): HasMany` → `Branch::class` (clave `city_id`). Definirla normal (sin quitar scopes); cuando necesites contar todas las sucursales sin importar la tienda, usa `$city->branches()->withoutTenancy()`.
  - Scope `scopeActive(Builder $q)` → `$q->where('is_active', true)`.

### 1.3 Factory `CityFactory`
- [x] Crear `database/factories/CityFactory.php` con `protected $model = City::class;` y:
  `name => $this->faker->unique()->city()`, `state => $this->faker->state()`, `country => 'VE'`, `is_active => true`. (El slug lo pone `HasSlug`.)

### 1.4 Policy `CityPolicy`
- [x] Crear `app/Domain/Locations/Policies/CityPolicy.php`. **Todos** los métodos (`viewAny, view, create, update, delete, deleteAny, restore, forceDelete, forceDeleteAny, restoreAny, replicate, reorder`) devuelven:
  ```php
  return method_exists($authUser, 'hasRole') && $authUser->hasRole('super_admin');
  ```
  Firma igual que `app/Policies/CompanyPolicy.php` (`AuthUser $authUser`, y `City $city` en los que reciben modelo).
- [x] Registrar en `AppServiceProvider::boot()`: `Gate::policy(City::class, CityPolicy::class);`

### 1.5 Recurso Filament `CityResource`
- [x] Crear `app/Filament/Resources/Cities/` copiando la estructura de `app/Filament/Resources/Branches/` (Resource + `Pages/{ListCities,CreateCity,EditCity}.php` + `Schemas/CityForm.php` + `Tables/CitiesTable.php`).
  - `$model = City::class`, `$navigationLabel = 'Ciudades'`, `$modelLabel = 'ciudad'`, `$pluralModelLabel = 'ciudades'`, `$navigationGroup = 'Configuración'`, icono `Heroicon::OutlinedMapPin`.
  - Form: `TextInput name` (required, `live(onBlur: true)`, `afterStateUpdated` que setea `slug` con `Str::slug($state)` — igual que `CompanyForm`), `TextInput slug` (required, `unique(ignoreRecord: true)`), `TextInput state` (label "Estado/Provincia"), `TextInput country` (label "País (ISO-2)", `maxLength(2)`, default `VE`), `Toggle is_active` (label "Activa", default true).
  - Tabla: columnas `name` (searchable, sortable), `state`, `country`, `is_active` (IconColumn boolean), `branches_count` (`->counts('branches')`, label "Sucursales"). Acción `EditAction`. **No** agregar borrado masivo (una ciudad con sucursales no debe borrarse; ver 1.6).
- [x] En `app/Providers/Filament/AdminPanelProvider.php`, agregar en `navigationGroups([...])` (antes de `'Sistema'`):
  ```php
  NavigationGroup::make('Catálogo')->collapsed(),
  NavigationGroup::make('Configuración')->collapsed(),
  ```

### 1.6 Protección al borrar
- [x] En `app/Filament/Resources/Cities/Pages/EditCity.php`:
  ```php
  protected function getHeaderActions(): array
  {
      return [
          DeleteAction::make()
              ->hidden(fn (City $record): bool => $record->branches()->withoutTenancy()->exists()),
      ];
  }
  ```
  (`use Filament\Actions\DeleteAction;`). La FK `restrictOnDelete()` de la Fase 2 es la segunda barrera en BD.

### 1.7 Tests — `tests/Feature/Catalog/CityResourceAccessTest.php`
Seguir el patrón de `tests/Feature/FilamentCompanyResourceIsolationTest.php` (RefreshDatabase, crear roles en `setUp`).
- [x] `test_super_admin_can_list_create_and_edit_cities` → `get('/admin/cities')->assertOk()`, `get('/admin/cities/create')->assertOk()`.
- [x] `test_store_owner_cannot_access_cities` → con rol `store_owner`: `/admin/cities` → `assertForbidden()`.
- [x] `test_city_slug_is_generated_automatically` → `City::create(['name' => 'El Tigre'])->slug === 'el-tigre'`.

### Verificación Fase 1
- [x] Implementado — [x] Probado (`php artisan migrate` + `php artisan test` en verde)
- [ ] Manual: como super_admin aparece el menú "Configuración → Ciudades" y se puede crear "El Tigre".

---

## FASE 2 — Sucursales: UUID, ciudad y WhatsApp

### 2.1 Migración
- [x] Crear `database/migrations/2026_11_12_000002_add_catalog_fields_to_branches_table.php`:
  ```php
  public function up(): void
  {
      Schema::table('branches', function (Blueprint $table) {
          $table->uuid('uuid')->nullable()->unique()->after('id');
          $table->foreignId('city_id')->nullable()->after('city')
              ->constrained('cities')->restrictOnDelete();
          $table->string('whatsapp_number', 20)->nullable()->after('phone');
      });

      // Rellenar UUID de sucursales existentes
      DB::table('branches')->whereNull('uuid')->orderBy('id')->each(function ($branch) {
          DB::table('branches')->where('id', $branch->id)->update(['uuid' => (string) Str::uuid()]);
      });
  }

  public function down(): void
  {
      Schema::table('branches', function (Blueprint $table) {
          $table->dropConstrainedForeignId('city_id');
          $table->dropUnique(['uuid']);
          $table->dropColumn(['uuid', 'whatsapp_number']);
      });
  }
  ```
  Imports: `Illuminate\Support\Facades\DB`, `Illuminate\Support\Str`.
  > No borrar la columna `city` (texto) existente: se mantiene por compatibilidad. La fuente de verdad pasa a ser `city_id`.

### 2.2 Modelo `Branch` (`app/Core/Branches/Models/Branch.php`)
- [x] Agregar trait `App\Core\Traits\HasUuid` (genera `uuid` al crear).
- [x] Agregar a `$fillable`: `'uuid'`, `'city_id'`, `'whatsapp_number'`.
- [x] Relación:
  ```php
  public function cityRecord(): BelongsTo
  {
      return $this->belongsTo(\App\Domain\Locations\Models\City::class, 'city_id');
  }
  ```
  > Se llama `cityRecord` porque `city` ya es una columna de texto; si la relación se llamara `city` chocaría con el atributo.
- [x] Mutator que deja solo dígitos:
  ```php
  protected function whatsappNumber(): Attribute
  {
      return Attribute::make(
          set: fn (?string $value) => $value === null || trim($value) === '' ? null : preg_replace('/\D+/', '', $value),
      );
  }
  ```
  (`use Illuminate\Database\Eloquent\Casts\Attribute;`)
- [x] Método de URL pública:
  ```php
  public function publicCatalogUrl(): ?string
  {
      $company = $this->company()->first();
      if (! $company || ! $this->uuid) {
          return null;
      }
      return route('public.catalog.show', ['companySlug' => $company->slug, 'branchUuid' => $this->uuid]);
  }
  ```
  La ruta `public.catalog.show` se crea en la Fase 7. Por eso en las Fases 2 a 6 **nada** debe llamar a este método (ni formularios, ni tablas, ni tests). El campo del enlace en el formulario, la acción "Ver catálogo" de la tabla y su test se agregan en la tarea **7.12**.
- [x] Método `isPubliclyAvailable(): bool` → `$this->status === 'active' && filled($this->whatsapp_number) && filled($this->uuid)`.
- [x] **No** cambiar `getRouteKeyName()` (Filament usa el `id` en las URLs del panel).

### 2.3 Factory `BranchFactory`
- [x] Agregar `'whatsapp_number' => '58414'.$this->faker->numerify('#######')`.
- [x] Agregar `'city_id' => null` (los tests que lo necesiten pasan `City::factory()`).

### 2.4 Formulario Filament `BranchForm` (`app/Filament/Resources/Branches/Schemas/BranchForm.php`)
- [x] Reemplazar el `TextInput::make('city')` por:
  ```php
  Select::make('city_id')
      ->label('Ciudad')
      ->relationship('cityRecord', 'name', fn (Builder $query) => $query->where('is_active', true))
      ->searchable()
      ->preload()
      ->required(fn (): bool => (bool) auth()->user()?->hasRole('super_admin')),
  ```
  > `required` condicional porque este campo queda deshabilitado para `store_owner` (ver abajo). Aplicar el mismo cambio al `->required()` existente del Select `company_id` y del Select `status`.
- [x] Agregar después de `phone`:
  ```php
  TextInput::make('whatsapp_number')
      ->label('WhatsApp para pedidos')
      ->helperText('Solo números, con código de país y sin "+". Ej: 584141234567')
      ->tel()
      ->required()
      ->regex('/^\+?[\d\s\-()]+$/')
      ->rule(fn () => function (string $attribute, $value, \Closure $fail): void {
          $digits = preg_replace('/\D+/', '', (string) $value);
          if (strlen($digits) < 10 || strlen($digits) > 15) {
              $fail('El WhatsApp debe tener entre 10 y 15 dígitos, incluyendo el código de país.');
          }
      }),
  ```
  (El mutator del modelo limpia espacios, guiones, paréntesis y `+`.)
- [x] Permisos de edición para `store_owner` (S1): agregar `->disabled(fn (): bool => ! auth()->user()?->hasRole('super_admin'))` a estos 4 campos: `company_id`, `city_id`, `is_main`, `status`. Los campos deshabilitados no se envían al guardar, así que el `store_owner` solo puede cambiar `name`, `address`, `country`, `timezone`, `phone`, `email` y `whatsapp_number`.

### 2.5 Tabla Filament `BranchesTable`
- [x] Cambiar la columna `city` por `TextColumn::make('cityRecord.name')->label('Ciudad')->searchable()`.
- [x] Agregar `TextColumn::make('whatsapp_number')->label('WhatsApp')`.
- [x] La acción "Ver catálogo" **no** se agrega aquí: va en la tarea 7.12.

### 2.6 Seeder existente
- [x] `database/seeders/BranchSeeder.php` no se modifica: el UUID lo genera `HasUuid` y `city_id`/`whatsapp_number` quedan `null` en la sede de "Acme Demo" (por eso esa sucursal no tiene catálogo público, lo cual es correcto según D2).

### 2.7 Tests — `tests/Feature/Catalog/BranchCatalogFieldsTest.php`
- [x] `test_branch_gets_uuid_on_create` → `Branch::factory()->create()->uuid` no vacío y válido (`Str::isUuid`).
- [x] `test_two_branches_have_different_uuids`.
- [x] `test_whatsapp_number_is_normalized_to_digits` → crear con `'+58 (414) 123-4567'` ⇒ guarda `584141234567`.
- [x] `test_empty_whatsapp_is_saved_as_null`.
- [x] `test_branch_belongs_to_city` → `$branch->cityRecord->is($city)`.
- [x] `test_city_with_branches_cannot_be_deleted` → `expectException(QueryException::class)` al hacer `$city->delete()`.

### Verificación Fase 2
- [x] Implementado — [x] Probado (`migrate` + `test` en verde, incluidos los 74 previos)
- [ ] Manual: en `/admin/branches` editar "Sede Central", asignar la ciudad "El Tigre" y WhatsApp `+58 414 000 0009`, guardar. Comprobar en BD:
  ```bash
  docker compose exec delivery-app php artisan tinker --execute="dump(App\Core\Branches\Models\Branch::withoutTenancy()->get(['id','uuid','city_id','whatsapp_number'])->toArray());"
  ```
  Esperado: `uuid` con valor, `city_id` con el id de El Tigre, `whatsapp_number` = `584140000009`.

---

## FASE 3 — Modelo de datos del catálogo

### 3.0 Diagrama
```
companies 1───N catalogs 1───N catalog_categories 1───N products 1───N product_option_groups 1───N product_options
    │               (is_default)                                        (single|multiple)
    └──N branches ──N── cities

Futuro (NO crear ahora): catalog_branch (catalog_id, branch_id), catalog_city (catalog_id, city_id),
branch_product_overrides (branch_id, product_id, price, is_available)
```
Todas las tablas del catálogo llevan `company_id` (para el aislamiento con `CompanyScope`) **y** su FK al padre.

### 3.1 Migraciones (una por tabla, en este orden)
- [x] `2026_11_12_000003_create_catalogs_table.php`
  ```php
  Schema::create('catalogs', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
      $table->string('name');
      $table->text('description')->nullable();
      $table->boolean('is_default')->default(false);
      $table->boolean('is_active')->default(true);
      $table->timestamps();
      $table->index(['company_id', 'is_default']);
  });
  ```
  > **NO** poner `unique('company_id')`: en el futuro una tienda podrá tener varios catálogos. La regla "un catálogo por defecto por tienda" se garantiza en código (Fase 4).
- [x] `2026_11_12_000004_create_catalog_categories_table.php`
  ```php
  Schema::create('catalog_categories', function (Blueprint $table) {
      $table->id();
      $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
      $table->foreignId('catalog_id')->constrained('catalogs')->cascadeOnDelete();
      $table->string('name');
      $table->text('description')->nullable();
      $table->unsignedInteger('sort_order')->default(0);
      $table->boolean('is_active')->default(true);
      $table->timestamps();
      $table->index(['catalog_id', 'is_active', 'sort_order']);
  });
  ```
- [x] `2026_11_12_000005_create_products_table.php`
  ```php
  Schema::create('products', function (Blueprint $table) {
      $table->id();
      $table->uuid('uuid')->unique();
      $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
      $table->foreignId('catalog_id')->constrained('catalogs')->cascadeOnDelete();
      $table->foreignId('catalog_category_id')->constrained('catalog_categories')->restrictOnDelete();
      $table->string('name');
      $table->text('description')->nullable();
      $table->decimal('price', 12, 2);
      $table->string('image_path')->nullable();
      $table->boolean('is_active')->default(true);     // visible en el catálogo
      $table->boolean('is_available')->default(true);  // false = "Agotado" (se ve pero no se puede pedir)
      $table->unsignedInteger('sort_order')->default(0);
      $table->timestamps();
      $table->index(['catalog_id', 'is_active']);
  });
  ```
- [x] `2026_11_12_000006_create_product_option_groups_table.php`
  ```php
  Schema::create('product_option_groups', function (Blueprint $table) {
      $table->id();
      $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
      $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
      $table->string('name');                                  // "Tamaño", "Extras"
      $table->string('selection_type', 10)->default('single'); // 'single' | 'multiple'
      $table->boolean('is_required')->default(false);
      $table->unsignedInteger('max_selections')->nullable();   // solo para 'multiple'; null = sin límite
      $table->unsignedInteger('sort_order')->default(0);
      $table->timestamps();
  });
  ```
- [x] `2026_11_12_000007_create_product_options_table.php`
  ```php
  Schema::create('product_options', function (Blueprint $table) {
      $table->id();
      $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
      $table->foreignId('product_option_group_id')->constrained('product_option_groups')->cascadeOnDelete();
      $table->string('name');                               // "Grande", "Queso extra"
      $table->decimal('price', 12, 2)->default(0);          // precio ADICIONAL (0 = sin costo)
      $table->boolean('is_active')->default(true);
      $table->unsignedInteger('sort_order')->default(0);
      $table->timestamps();
  });
  ```

### 3.2 Enum de tipo de selección
- [x] Crear `app/Domain/Catalog/Enums/OptionSelectionType.php`:
  ```php
  enum OptionSelectionType: string
  {
      case Single = 'single';
      case Multiple = 'multiple';

      public function label(): string
      {
          return match ($this) {
              self::Single => 'Elegir una',
              self::Multiple => 'Elegir varias',
          };
      }
  }
  ```

### 3.3 Modelos (todos en `app/Domain/Catalog/Models/`, todos `extends \App\Core\Support\TenantModel`, todos `use HasFactory`)

**Regla común (R2):** cada modelo hijo define en `booted()` un listener `saving` (cubre crear y editar) que copia `company_id` desde el padre, sobrescribiendo lo que haya puesto `HasCompany`. Ejemplo para `CatalogCategory`:
```php
protected static function booted(): void
{
    static::saving(function (self $model): void {
        $model->company_id = Catalog::withoutTenancy()->whereKey($model->catalog_id)->value('company_id');
    });
}
```
Si el padre no existe, `company_id` queda `null` y la base de datos rechaza el registro (FK/NOT NULL): ese es el comportamiento esperado, no agregues manejo extra.

- [x] **`Catalog`**
  - traits: `HasFactory`, `App\Core\Traits\HasUuid`
  - `$fillable = ['company_id', 'name', 'description', 'is_default', 'is_active']`
  - casts: `is_default`, `is_active` → boolean
  - relaciones: `categories(): HasMany` (CatalogCategory, ordenado `orderBy('sort_order')`), `products(): HasMany` (Product)
  - `company()` ya viene de `HasCompany`.
  - Aquí `company_id` **sí** se pasa explícito siempre (no tiene padre de catálogo).
- [x] **`CatalogCategory`** (tabla `catalog_categories` → definir `protected $table = 'catalog_categories';`)
  - `$fillable = ['catalog_id', 'name', 'description', 'sort_order', 'is_active']` (sin `company_id`: se deriva)
  - casts: `is_active` boolean, `sort_order` integer
  - relaciones: `catalog(): BelongsTo`, `products(): HasMany` (FK `catalog_category_id`, ordenado por `sort_order`)
  - `booted()`: deriva `company_id` del `Catalog`.
- [x] **`Product`**
  - traits: `HasFactory`, `HasUuid`
  - `$fillable = ['catalog_id', 'catalog_category_id', 'name', 'description', 'price', 'image_path', 'is_active', 'is_available', 'sort_order']`
  - casts: `price` → `'decimal:2'`, `is_active`, `is_available` → boolean, `sort_order` → integer
  - relaciones: `catalog()`, `category(): BelongsTo` (`CatalogCategory::class, 'catalog_category_id'`), `optionGroups(): HasMany` (ProductOptionGroup, ordenado `sort_order`)
  - `booted()` → en `saving`:
    1. Derivar `company_id` del `Catalog`.
    2. Validar que la categoría pertenece al mismo catálogo; si no, lanzar:
       ```php
       throw \Illuminate\Validation\ValidationException::withMessages([
           'catalog_category_id' => 'La categoría no pertenece a este catálogo.',
       ]);
       ```
       (consultar con `CatalogCategory::withoutTenancy()->whereKey($model->catalog_category_id)->value('catalog_id')`).
  - método normal (no accessor de Eloquent) `public function imageUrl(): ?string` → `return $this->image_path ? Storage::disk('public')->url($this->image_path) : null;` (`use Illuminate\Support\Facades\Storage;`).
- [x] **`ProductOptionGroup`**
  - `$fillable = ['product_id', 'name', 'selection_type', 'is_required', 'max_selections', 'sort_order']`
  - casts: `is_required` → boolean, `max_selections`/`sort_order` → integer. **`selection_type` NO lleva cast** (queda string `'single'`/`'multiple'`).
  - relaciones: `product(): BelongsTo`, `options(): HasMany` (ProductOption, FK `product_option_group_id`, ordenado `sort_order`)
  - `booted()` → `saving`: derivar `company_id` del `Product` (`Product::withoutTenancy()->whereKey($model->product_id)->value('company_id')`). Si `$model->selection_type === OptionSelectionType::Single->value`, forzar `max_selections = 1`.
- [x] **`ProductOption`**
  - `$fillable = ['product_option_group_id', 'name', 'price', 'is_active', 'sort_order']`
  - casts: `price` → `'decimal:2'`, `is_active` → boolean, `sort_order` → integer
  - relación: `group(): BelongsTo` (`ProductOptionGroup::class, 'product_option_group_id'`)
  - `booted()` → `saving`: derivar `company_id` del grupo.
- [x] En `Company` (`app/Core/Companies/Models/Company.php`) agregar relaciones:
  ```php
  public function catalogs(): HasMany { return $this->hasMany(Catalog::class); }
  public function defaultCatalog(): HasOne { return $this->hasOne(Catalog::class)->where('is_default', true); }
  ```

### 3.4 Factories (en `database/factories/`)
- [x] `CatalogFactory`: `company_id => Company::factory()`, `name => 'Catálogo secundario'`, **`is_default => false`**, `is_active => true`. (El catálogo por defecto lo crea siempre el listener de la Fase 4; la factory nunca crea uno por defecto para no duplicar — R10.)
- [x] `CatalogCategoryFactory`: `catalog_id => fn () => Company::factory()->create()->defaultCatalog()->withoutTenancy()->value('id')`, `name => faker->unique()->word()`, `sort_order => 0`, `is_active => true`. (Así, por defecto, la categoría queda en el catálogo por defecto de una tienda nueva.)
  > Esta factory depende del listener de la Fase 4. Hasta terminar la Fase 4, en los tests de la Fase 3 pasa siempre `catalog_id` explícito creado con `Catalog::factory()->create()`.
- [x] `ProductFactory`: en el array de `definition()`, **`catalog_category_id` debe ir ANTES que `catalog_id`** (Laravel resuelve los atributos en orden y el closure necesita el id ya creado):
  ```php
  'catalog_category_id' => CatalogCategory::factory(),
  'catalog_id' => fn (array $attributes) => CatalogCategory::withoutTenancy()->whereKey($attributes['catalog_category_id'])->value('catalog_id'),
  ```
  Luego `name => faker->words(2, true)`, `description => faker->sentence()`, `price => faker->randomFloat(2, 1, 30)`, `is_active => true`, `is_available => true`, `sort_order => 0`.
- [x] `ProductOptionGroupFactory`: `product_id => Product::factory()`, `name => 'Tamaño'`, `selection_type => 'single'`, `is_required => false`.
- [x] `ProductOptionFactory`: `product_option_group_id => ProductOptionGroup::factory()`, `name`, `price => 0`, `is_active => true`.

> **Forma única de obtener el catálogo por defecto de una tienda en tests y seeders:**
> ```php
> $catalog = $company->defaultCatalog()->withoutTenancy()->first();
> ```
> Siempre con `withoutTenancy()`: si el test está logueado como usuario de otra tienda, `CompanyScope` devolvería `null` (R1). No usar la propiedad mágica `$company->defaultCatalog`.

### 3.5 Tests — `tests/Feature/Catalog/CatalogModelTest.php`
- [x] `test_category_company_id_is_derived_from_catalog` — loguear un `super_admin` cuya empresa activa es A; crear categoría en catálogo de B ⇒ `company_id === B`.
- [x] `test_product_company_id_is_derived_from_catalog` (mismo escenario).
- [x] `test_option_group_and_option_company_id_are_derived`.
- [x] `test_product_with_category_from_other_catalog_is_rejected` ⇒ `expectException(ValidationException::class)`.
- [x] `test_single_selection_group_forces_max_one`.
- [x] `test_deleting_catalog_cascades_categories_and_products` (usar `withoutTenancy()` para contar).
- [x] `test_category_with_products_cannot_be_deleted` ⇒ `QueryException`.

### Verificación Fase 3
- [x] Implementado — [x] Probado

---

## FASE 4 — Catálogo por defecto automático al crear una tienda

### 4.1 Action idempotente
- [x] Crear `app/Domain/Catalog/Actions/EnsureDefaultCatalogAction.php`:
  ```php
  class EnsureDefaultCatalogAction
  {
      public function execute(Company $company): Catalog
      {
          return DB::transaction(function () use ($company) {
              $existing = Catalog::withoutTenancy()
                  ->where('company_id', $company->id)
                  ->where('is_default', true)
                  ->lockForUpdate()
                  ->first();

              return $existing ?? Catalog::withoutTenancy()->create([
                  'company_id' => $company->id,
                  'name' => 'Catálogo de '.$company->name,
                  'is_default' => true,
                  'is_active' => true,
              ]);
          });
      }
  }
  ```
  > Debe pasar `company_id` explícito (R2).

### 4.2 Listener
- [x] Crear `app/Domain/Catalog/Listeners/CreateDefaultCatalogForCompany.php` con `public function handle(CompanyCreated $event): void` que llame a `EnsureDefaultCatalogAction::execute($event->company)` (el evento expone `public readonly Company $company`).
  - **No** implementar `ShouldQueue` (debe ser síncrono: el catálogo tiene que existir apenas se crea la tienda).
- [x] Registrar en `AppServiceProvider::boot()`:
  `Event::listen(CompanyCreated::class, CreateDefaultCatalogForCompany::class);`

### 4.3 Comando para tiendas existentes
- [x] Crear `app/Console/Commands/CatalogEnsureDefaultsCommand.php` (Laravel 12 registra automáticamente los comandos de `app/Console/Commands`):
  ```php
  protected $signature = 'catalog:ensure-defaults';
  protected $description = 'Crea el catálogo por defecto en las tiendas que no lo tengan';

  public function handle(EnsureDefaultCatalogAction $action): int
  {
      $created = 0;
      foreach (Company::query()->cursor() as $company) {
          if ($action->execute($company)->wasRecentlyCreated) {
              $created++;
          }
      }
      $this->info("Catálogos creados: {$created}");
      return self::SUCCESS;
  }
  ```
- [x] Ejecutarlo una vez en desarrollo: `docker compose exec delivery-app php artisan catalog:ensure-defaults`. (Salida: `Catálogos creados: 1`)

### 4.4 Servicio resolvedor de catálogo (punto de extensión futuro)
- [x] Crear `app/Domain/Catalog/Services/CatalogResolver.php`:
  ```php
  /**
   * Decide qué catálogo ve una sucursal.
   * Hoy: el catálogo por defecto y activo de la tienda.
   * Futuro (solo agregando tablas): 1) catalog_branch, 2) catalog_city, 3) defecto de la tienda.
   */
  class CatalogResolver
  {
      public function forBranch(Branch $branch): ?Catalog
      {
          return Catalog::withoutTenancy()
              ->where('company_id', $branch->company_id)
              ->where('is_default', true)
              ->where('is_active', true)
              ->first();
      }
  }
  ```
  > El catálogo público usa **siempre** este servicio para saber qué catálogo mostrar. **Nunca** consultar `is_default` directamente en el controlador público. (El panel sí usa `defaultCatalog()` solo para preseleccionar el catálogo en los formularios.)

### 4.5 Tests — `tests/Feature/Catalog/DefaultCatalogCreationTest.php`
- [x] `test_creating_company_creates_one_default_catalog`.
- [x] `test_action_is_idempotent` (llamarla 2 veces ⇒ sigue habiendo 1).
- [x] `test_command_creates_missing_default_catalogs` (borrar el catálogo de una tienda con `withoutTenancy()->delete()`, correr `$this->artisan('catalog:ensure-defaults')->assertSuccessful()`, ⇒ existe de nuevo).
- [x] `test_creating_company_from_filament_creates_default_catalog` (super_admin con rol creado en `setUp`, `Livewire::test(CreateCompany::class)->fillForm(['name' => 'Tienda Test', 'slug' => 'tienda-test'])->call('create')->assertHasNoFormErrors()`; luego existe 1 catálogo con `is_default = true` para `slug = tienda-test`).
- [x] `test_resolver_returns_default_active_catalog_of_branch_company`.
- [x] `test_resolver_returns_null_when_catalog_inactive`.

### Verificación Fase 4
- [x] Implementado — [x] Probado
- [x] Manual: crear una tienda nueva desde `/admin/companies/create` y comprobar:
  ```bash
  docker compose exec delivery-app php artisan tinker --execute="dump(App\Domain\Catalog\Models\Catalog::withoutTenancy()->get(['id','company_id','name','is_default'])->toArray());"
  ```
  Esperado: una fila con `is_default = true` por cada tienda.

---

## FASE 5 — Roles, permisos y policies

### 5.1 Proteger policies personalizadas
- [x] En `database/seeders/DatabaseSeeder.php`, cambiar:
  ```php
  Artisan::call('shield:generate', ['--all' => true, '--panel' => 'admin']);
  ```
  por:
  ```php
  Artisan::call('shield:generate', ['--all' => true, '--panel' => 'admin', '--ignore-existing-policies' => true]);
  ```

### 5.2 Rol `store_owner`
- [x] `app/Support/RoleLabel.php`: agregar `'store_owner' => 'Dueño de tienda'`.
- [x] `app/Models/User.php` → `canAccessPanel`: agregar `'store_owner'` a la lista de roles del panel `admin`.
- [x] `database/seeders/RoleSeeder.php`:
  - En `BUSINESS_PERMISSIONS` agregar `'store_owner' => []`.
  - En `SHIELD_PERMISSION_SUFFIXES` agregar:
    ```php
    'store_owner' => [
        'ViewAny:Company', 'View:Company',
        'ViewAny:Branch', 'View:Branch', 'Update:Branch',
        'ViewAny:Catalog', 'View:Catalog', 'Update:Catalog',
        'ViewAny:CatalogCategory', 'View:CatalogCategory', 'Create:CatalogCategory', 'Update:CatalogCategory', 'Delete:CatalogCategory', 'DeleteAny:CatalogCategory', 'Reorder:CatalogCategory',
        'ViewAny:Product', 'View:Product', 'Create:Product', 'Update:Product', 'Delete:Product', 'DeleteAny:Product', 'Reorder:Product',
    ],
    ```
  - **No** agregar permisos del catálogo ni de ciudades al rol `admin` (S1).
  > Los nombres `Accion:Modelo` los genera Shield a partir de los Resources de la Fase 6. El seeder ya filtra los que no existan, así que el orden no rompe nada, pero **hay que volver a correr el seeder después de la Fase 6** (Tarea 6.7).

### 5.3 Policies del catálogo
Crear en `app/Domain/Catalog/Policies/` (esa es la ruta donde Shield las buscaría, así `--ignore-existing-policies` las respeta):
- [x] `CatalogPolicy`, `CatalogCategoryPolicy`, `ProductPolicy`.
- [x] Copiar la estructura de `app/Policies/CompanyPolicy.php`, cambiando:
  - Permisos: `ViewAny:Product`, `View:Product`, etc. (según el modelo).
  - `canAccessCompany` → reemplazar por `canAccessRecord(AuthUser $u, Model $record, string $perm)` (`use Illuminate\Database\Eloquent\Model;`):
    ```php
    if ($this->isSuperAdmin($u)) return true;
    return $u->can($perm)
        && $u instanceof User
        && $u->companies()->whereKey($record->company_id)->exists();
    ```
- [x] `CatalogPolicy`: `create`, `delete`, `deleteAny`, `forceDelete*`, `restore*`, `replicate` → **solo super_admin** (el catálogo por defecto no se crea ni borra a mano).
- [x] Registrar las 3 en `AppServiceProvider::boot()` con `Gate::policy(...)`.
- [x] `BranchPolicy` (`app/Core/Branches/Policies/BranchPolicy.php`): `create`, `delete`, `deleteAny` deben devolver `true` **solo** para `super_admin` (S2). `view`/`update` deben exigir el permiso **y** pertenencia (`$authUser->companies()->whereKey($branch->company_id)->exists()`), igual que `CompanyPolicy`. Hoy **no** está registrada explícitamente: agregar `Gate::policy(Branch::class, BranchPolicy::class);` en `AppServiceProvider::boot()` (R4). Para los métodos restantes (`restore*`, `forceDelete*`, `replicate`, `reorder`) devolver `true` solo para `super_admin`.

### 5.4 Panel: datos de la tienda para `store_owner`
- [x] No requiere código: `CompanyResource::scopeToUser()` y `CompanyPolicy` ya limitan a las tiendas del usuario, y `store_owner` no tiene `Create:Company`. Se comprueba con el test `test_store_owner_sees_only_own_company_and_cannot_create` de 5.6.

### 5.5 Pertenencia a la tienda al crear usuarios
- [x] Crear el trait `app/Filament/Resources/Users/Concerns/SyncsCompanyMembership.php`:
  ```php
  namespace App\Filament\Resources\Users\Concerns;

  trait SyncsCompanyMembership
  {
      protected function syncCompanyMembership(): void
      {
          $user = $this->record;

          if (! $user->company_id) {
              return;
          }

          $user->companies()->syncWithoutDetaching([$user->company_id => ['role' => 'owner']]);

          if (! $user->current_company_id) {
              $user->forceFill(['current_company_id' => $user->company_id])->save();
          }
      }
  }
  ```
- [x] En `app/Filament/Resources/Users/Pages/CreateUser.php`: `use SyncsCompanyMembership;` y
  ```php
  protected function afterCreate(): void
  {
      $this->syncCompanyMembership();
  }
  ```
- [x] En `app/Filament/Resources/Users/Pages/EditUser.php`: `use SyncsCompanyMembership;` y
  ```php
  protected function afterSave(): void
  {
      $this->syncCompanyMembership();
  }
  ```

### 5.6 Tests — `tests/Feature/Catalog/CatalogAuthorizationTest.php`
En `setUp()`:
- Crear con `Permission::findOrCreate($p, 'web')` **exactamente** la lista de permisos de `store_owner` de la tarea 5.2, más `Create:Company`, `Create:Branch`, `Delete:Branch`, `Create:Catalog`, `Delete:Catalog`.
- Copiar el array de permisos de `store_owner` de la tarea 5.2 en una constante de la clase de test `private const STORE_OWNER_PERMISSIONS = [...]` (mismos valores, mismo orden).
- `Role::findOrCreate('super_admin', 'web')`; `Role::findOrCreate('store_owner', 'web')->syncPermissions(self::STORE_OWNER_PERMISSIONS)`.

Helper obligatorio en la clase de test (cópialo igual en todos los tests de las Fases 5–7 que lo necesiten):
```php
private function makeStoreOwner(Company $company): User
{
    $user = User::factory()->create([
        'company_id' => $company->id,
        'current_company_id' => $company->id,
    ]);
    $user->companies()->attach($company->id, ['role' => 'owner']);
    $user->assignRole('store_owner');

    return $user;
}
```
Tests:
- [x] `test_store_owner_sees_only_own_company_and_cannot_create` (`CompanyResource::getEloquentQuery()->pluck('id')` solo contiene su tienda; `Gate::allows('create', Company::class)` es `false`).
- [x] `test_store_owner_can_view_and_update_own_product`.
- [x] `test_store_owner_cannot_view_or_update_product_of_other_store`.
- [x] `test_store_owner_cannot_create_or_delete_catalog`.
- [x] `test_store_owner_cannot_create_or_delete_branch_but_can_update_own`.
- [x] `test_store_owner_cannot_update_branch_of_other_store`.
- [x] `test_super_admin_can_do_everything`.
- [x] `test_user_created_from_filament_with_company_gets_membership`.

### Verificación Fase 5
- [x] Implementado — [x] Probado

---

## FASE 6 — Panel Filament del catálogo

Todos los recursos copian la estructura de carpetas de `app/Filament/Resources/Branches/`. `navigationGroup = 'Catálogo'`.

### 6.1 Clase de apoyo para los Select del catálogo (aislamiento + etiqueta)
- [x] Crear `app/Filament/Support/CatalogSelect.php`. **Todos** los formularios usan esta clase para elegir catálogo; no repitas la lógica:
  ```php
  namespace App\Filament\Support;

  use App\Core\Tenancy\TenantContext;
  use App\Domain\Catalog\Models\Catalog;
  use Filament\Forms\Components\Select;
  use Illuminate\Database\Eloquent\Builder;

  class CatalogSelect
  {
      public static function make(): Select
      {
          return Select::make('catalog_id')
              ->label('Catálogo')
              ->relationship('catalog', 'name', fn (Builder $query): Builder => static::scope($query))
              ->getOptionLabelFromRecordUsing(fn (Catalog $record): string => static::isSuperAdmin()
                  ? $record->company?->name.' — '.$record->name
                  : $record->name)
              ->default(fn (): ?int => app(TenantContext::class)->company()?->defaultCatalog()->withoutTenancy()->value('id'))
              ->searchable()
              ->preload()
              ->required();
      }

      /** Filtro explícito por tienda (además del CompanyScope). */
      public static function scope(Builder $query): Builder
      {
          return static::isSuperAdmin()
              ? $query
              : $query->where('company_id', app(TenantContext::class)->companyId());
      }

      public static function isSuperAdmin(): bool
      {
          return (bool) auth()->user()?->hasRole('super_admin');
      }
  }
  ```

### 6.2 `CatalogResource` ("Catálogos")
- [x] `app/Filament/Resources/Catalogs/CatalogResource.php`: `$model = Catalog::class`, `$navigationGroup = 'Catálogo'`, `$navigationLabel = 'Catálogos'`, `$modelLabel = 'catálogo'`, `$pluralModelLabel = 'catálogos'`, icono `Heroicon::OutlinedBookOpen`, `$navigationSort = 1`.
- [x] `getPages()`: solo `'index' => ListCatalogs::route('/')` y `'edit' => EditCatalog::route('/{record}/edit')`. **Sin** página `create`. En `ListCatalogs` `getHeaderActions()` devuelve `[]` (sin botón "Crear").
- [x] `EditCatalog::getHeaderActions()` devuelve `[]` (sin botón borrar).
- [x] Form (`Schemas/CatalogForm.php`): `TextInput name` (label "Nombre", required, maxLength 150), `Textarea description` (label "Descripción", maxLength 1000), `Toggle is_active` (label "Publicado", helperText "Si lo desactivas, los enlaces de todas las sucursales mostrarán la página de error.").
- [x] Tabla (`Tables/CatalogsTable.php`):
  - `TextColumn::make('company.name')->label('Tienda')->searchable()->visible(fn (): bool => CatalogSelect::isSuperAdmin())`
  - `TextColumn::make('name')->label('Nombre')->searchable()`
  - `IconColumn::make('is_default')->label('Por defecto')->boolean()`
  - `IconColumn::make('is_active')->label('Publicado')->boolean()`
  - `TextColumn::make('categories_count')->label('Categorías')->counts('categories')`
  - `TextColumn::make('products_count')->label('Productos')->counts('products')`
  - `recordActions([EditAction::make()])`, `toolbarActions([])` (sin borrado masivo).

### 6.3 `CatalogCategoryResource` ("Categorías")
- [x] `app/Filament/Resources/CatalogCategories/CatalogCategoryResource.php`: `$model = CatalogCategory::class`, `$navigationGroup = 'Catálogo'`, `$navigationLabel = 'Categorías'`, `$modelLabel = 'categoría'`, `$pluralModelLabel = 'categorías'`, icono `Heroicon::OutlinedTag`, `$navigationSort = 2`. Páginas `index`, `create`, `edit`.
- [x] Form (`Schemas/CatalogCategoryForm.php`):
  ```php
  CatalogSelect::make(),
  TextInput::make('name')->label('Nombre')->required()->maxLength(100),
  Textarea::make('description')->label('Descripción')->maxLength(500),
  TextInput::make('sort_order')->label('Orden')->numeric()->minValue(0)->default(0),
  Toggle::make('is_active')->label('Visible')->default(true),
  ```
- [x] Tabla (`Tables/CatalogCategoriesTable.php`):
  - Columnas: `name` (label "Nombre", searchable), `catalog.company.name` (label "Tienda", `->visible(fn (): bool => CatalogSelect::isSuperAdmin())`), `products_count` (`->counts('products')`, label "Productos"), `IconColumn is_active` (label "Visible"), `sort_order` (label "Orden", sortable).
  - `->reorderable('sort_order')->defaultSort('sort_order')`.
  - `recordActions([EditAction::make(), DeleteAction::make()->hidden(fn (CatalogCategory $record): bool => $record->products()->withoutTenancy()->exists())])`.
  - `toolbarActions([])` (**sin** borrado masivo: saltaría la protección anterior).
- [x] `EditCatalogCategory::getHeaderActions()`:
  ```php
  return [DeleteAction::make()->hidden(fn (CatalogCategory $record): bool => $record->products()->withoutTenancy()->exists())];
  ```

### 6.4 `ProductResource` ("Productos")
- [x] `app/Filament/Resources/Products/ProductResource.php`: `$model = Product::class`, `$navigationGroup = 'Catálogo'`, `$navigationLabel = 'Productos'`, `$modelLabel = 'producto'`, `$pluralModelLabel = 'productos'`, icono `Heroicon::OutlinedShoppingBag`, `$navigationSort = 3`. Páginas `index`, `create`, `edit`.
- [x] Form (`Schemas/ProductForm.php`). Imports: `Filament\Schemas\Components\Section`, `Filament\Schemas\Components\Utilities\Get`, `Filament\Schemas\Components\Utilities\Set`, `Filament\Forms\Components\{Select, TextInput, Textarea, Toggle, FileUpload, Repeater}`, `App\Domain\Catalog\Enums\OptionSelectionType`, `App\Domain\Catalog\Models\Catalog`, `App\Filament\Support\CatalogSelect`, `Illuminate\Database\Eloquent\Builder`.
  ```php
  return $schema->components([
      Section::make('Datos del producto')->columns(2)->schema([
          CatalogSelect::make()
              ->live()
              ->afterStateUpdated(fn (Set $set) => $set('catalog_category_id', null)),
          Select::make('catalog_category_id')
              ->label('Categoría')
              ->relationship('category', 'name', fn (Builder $query, Get $get): Builder => CatalogSelect::scope($query)->where('catalog_id', $get('catalog_id')))
              ->searchable()
              ->preload()
              ->required(),
          TextInput::make('name')->label('Nombre')->required()->maxLength(150),
          TextInput::make('price')
              ->label('Precio')
              ->numeric()
              ->required()
              ->minValue(0)
              ->step(0.01)
              ->prefix(fn (Get $get): string => Catalog::withoutTenancy()->find($get('catalog_id'))?->company?->currency ?: 'USD'),
          Textarea::make('description')->label('Descripción')->maxLength(1000)->columnSpanFull(),
          FileUpload::make('image_path')
              ->label('Imagen')
              ->image()
              ->disk('public')
              ->directory('products')
              ->visibility('public')
              ->maxSize(2048)
              ->imageEditor()
              ->columnSpanFull(),
          Toggle::make('is_active')->label('Visible en el catálogo')->default(true),
          Toggle::make('is_available')->label('Disponible (desactivar = Agotado)')->default(true),
          TextInput::make('sort_order')->label('Orden')->numeric()->minValue(0)->default(0),
      ]),
      Section::make('Opciones y extras')->schema([
          Repeater::make('optionGroups')
              ->label('Grupos de opciones')
              ->relationship()
              ->reorderable()
              ->orderColumn('sort_order')
              ->collapsible()
              ->defaultItems(0)
              ->addActionLabel('Agregar grupo')
              ->itemLabel(fn (array $state): string => $state['name'] ?? 'Grupo')
              ->schema([
                  TextInput::make('name')->label('Nombre del grupo')->placeholder('Ej: Tamaño, Extras, Salsas')->required()->maxLength(100),
                  Select::make('selection_type')
                      ->label('Tipo')
                      ->options(collect(OptionSelectionType::cases())->mapWithKeys(fn (OptionSelectionType $c) => [$c->value => $c->label()])->all())
                      ->default(OptionSelectionType::Single->value)
                      ->required()
                      ->live(),
                  Toggle::make('is_required')->label('Obligatorio')->default(false),
                  TextInput::make('max_selections')
                      ->label('Máximo a elegir (vacío = sin límite)')
                      ->numeric()
                      ->minValue(1)
                      ->visible(fn (Get $get): bool => $get('selection_type') === OptionSelectionType::Multiple->value),
                  Repeater::make('options')
                      ->label('Opciones')
                      ->relationship()
                      ->reorderable()
                      ->orderColumn('sort_order')
                      ->addActionLabel('Agregar opción')
                      ->schema([
                          TextInput::make('name')->label('Opción')->required()->maxLength(100),
                          TextInput::make('price')->label('Precio adicional')->numeric()->default(0)->minValue(0)->step(0.01),
                          Toggle::make('is_active')->label('Activa')->default(true),
                      ])
                      ->columns(3)
                      ->minItems(1)
                      ->defaultItems(1),
              ]),
      ]),
  ]);
  ```
  > `selection_type` se guarda y se lee como **string** (`'single'`/`'multiple'`); el modelo **no** lo castea a enum (ver 3.3). El enum solo se usa para las opciones y etiquetas. Por eso las comparaciones son contra `->value`.
- [x] Tabla (`Tables/ProductsTable.php`):
  - `ImageColumn::make('image_path')->label('')->disk('public')->circular()`
  - `TextColumn::make('name')->label('Nombre')->searchable()->sortable()`
  - `TextColumn::make('category.name')->label('Categoría')`
  - `TextColumn::make('price')->label('Precio')->money(fn (Product $record): string => $record->company?->currency ?: 'USD')`
  - `IconColumn::make('is_active')->label('Visible')->boolean()`
  - `ToggleColumn::make('is_available')->label('Disponible')`
  - `TextColumn::make('catalog.company.name')->label('Tienda')->visible(fn (): bool => CatalogSelect::isSuperAdmin())`
  - Filtros: `SelectFilter::make('catalog_category_id')->label('Categoría')->relationship('category', 'name', fn (Builder $q): Builder => CatalogSelect::scope($q))`, `TernaryFilter::make('is_available')->label('Disponible')`.
  - `->defaultSort('sort_order')`
  - `recordActions([EditAction::make(), DeleteAction::make()])`, `toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])`.
  - **No** agregar `ReplicateAction`.

### 6.5 Aislamiento en los Select
- [x] Ya resuelto con `CatalogSelect::scope()` (6.1) en todos los Select y filtros de catálogo/categoría. Comprobar que **ningún** `relationship()` hacia `catalog` o `category` se escribió sin ese closure.

### 6.6 Enlace de catálogo visible para la tienda
- [x] Se hace en la tarea 7.12 (necesita la ruta pública).

### 6.7 Regenerar permisos
- [x] Ejecutar (no borra datos):
  ```bash
  docker compose exec delivery-app php artisan shield:generate --all --panel=admin --ignore-existing-policies
  docker compose exec delivery-app php artisan db:seed --class=PermissionSeeder
  docker compose exec delivery-app php artisan db:seed --class=RoleSeeder
  ```
- [x] Comprobar con `git status` que Shield **no** modificó `app/Domain/Catalog/Policies/*`, `app/Domain/Locations/Policies/*` ni `app/Core/Branches/Policies/BranchPolicy.php` (R5 garantiza que no). Shield puede crear archivos nuevos de policies para otros modelos del starter: **no los borres ni edites**; no se usan porque `Gate::policy()` explícito tiene prioridad (R4). Inclúyelos en el commit de la fase.
- [x] Comprobar que existen los permisos nuevos:
  ```bash
  docker compose exec delivery-app php artisan tinker --execute="dump(Spatie\Permission\Models\Permission::where('name','like','%:Product')->pluck('name')->all());"
  ```
  Esperado: incluye `ViewAny:Product`, `Create:Product`, `Update:Product`, `Delete:Product`, `Reorder:Product`.

### 6.8 Tests — `tests/Feature/Catalog/FilamentCatalogIsolationTest.php`
`setUp()` igual que 5.6 (permisos, roles y helper `makeStoreOwner`). En los tests que llenan Repeaters, llamar primero `$undoRepeaterFake = Repeater::fake();` y al final `$undoRepeaterFake();` (`use Filament\Forms\Components\Repeater;`). Con `Repeater::fake()` los ítems del repeater se pasan como lista normal.
- [x] `test_store_owner_lists_only_own_products` (`Livewire::test(ListProducts::class)->assertCanSeeTableRecords([$own])->assertCanNotSeeTableRecords([$foreign])`).
- [x] `test_store_owner_cannot_open_edit_page_of_foreign_product` (`get("/admin/products/{$foreign->id}/edit")->assertNotFound()`).
- [x] `test_store_owner_creates_product_with_option_groups`:
  ```php
  Livewire::test(CreateProduct::class)
      ->fillForm([
          'catalog_id' => $catalog->id,
          'catalog_category_id' => $category->id,
          'name' => 'Hamburguesa Test',
          'price' => 5.50,
          'is_active' => true,
          'is_available' => true,
          'optionGroups' => [[
              'name' => 'Tamaño',
              'selection_type' => 'single',
              'is_required' => true,
              'options' => [
                  ['name' => 'Normal', 'price' => 0, 'is_active' => true],
                  ['name' => 'Doble', 'price' => 2, 'is_active' => true],
              ],
          ]],
      ])
      ->call('create')
      ->assertHasNoFormErrors();
  ```
  Verificar con `withoutTenancy()`: 1 producto, 1 grupo con `max_selections = 1`, 2 opciones; todos con `company_id` de la tienda.
- [x] `test_super_admin_creates_product_for_any_store_and_company_id_matches_catalog` (super_admin con `current_company_id` = tienda A crea producto en catálogo de B ⇒ `company_id` = B).
- [x] `test_store_owner_lists_only_own_categories`.
- [x] `test_catalog_resource_has_no_create_page` (`get('/admin/catalogs/create')->assertNotFound()`).
- [x] `test_category_with_products_has_no_delete_action` (`Livewire::test(EditCatalogCategory::class, ['record' => $category->getRouteKey()])->assertActionHidden(DeleteAction::class)`).

### Verificación Fase 6
- [x] Implementado — [x] Probado
- [ ] Manual (como super_admin): crear 2 categorías y 3 productos con opciones (uno con "Tamaño" obligatorio single y otro con "Extras" multiple máx 3). Subir una imagen. Marcar un producto como agotado.
- [ ] Manual (como store_owner): solo ve su tienda, su catálogo, sus categorías y productos. No ve "Ciudades".

---

## FASE 7 — Catálogo público y pedido por WhatsApp

### 7.1 Rate limiter
- [x] En `AppServiceProvider::boot()`:
  ```php
  RateLimiter::for('public-catalog', fn (Request $request) => Limit::perMinute(60)->by($request->ip()));
  ```
  (`use Illuminate\Support\Facades\RateLimiter; use Illuminate\Cache\RateLimiting\Limit; use Illuminate\Http\Request;`)

### 7.2 Rutas (`routes/web.php`, antes de `require __DIR__.'/auth.php';`)
- [x] ```php
  use App\Http\Controllers\PublicCatalog\PublicCatalogController;

  Route::prefix('c')->name('public.catalog.')->middleware('throttle:public-catalog')->group(function () {
      Route::get('{companySlug}/{branchUuid}', [PublicCatalogController::class, 'show'])
          ->where('companySlug', '[a-z0-9\-]+')
          ->whereUuid('branchUuid')
          ->name('show');

      // Cualquier otra forma (sin UUID, UUID inválido, segmentos extra) => error
      Route::get('{any?}', [PublicCatalogController::class, 'invalid'])
          ->where('any', '.*')
          ->name('invalid');
  });
  ```
- [x] Verificar con `php artisan route:list --path=c`.

### 7.3 Presentador (convierte el catálogo en un array plano para la vista/JS)
- [x] Crear `app/Domain/Catalog/Services/PublicCatalogPresenter.php` con este código (R1: todas las consultas con `withoutTenancy()`):
  ```php
  namespace App\Domain\Catalog\Services;

  use App\Core\Branches\Models\Branch;
  use App\Core\Companies\Models\Company;
  use App\Domain\Catalog\Models\Catalog;
  use App\Domain\Catalog\Models\CatalogCategory;
  use App\Domain\Catalog\Models\Product;
  use App\Domain\Catalog\Models\ProductOption;
  use App\Domain\Catalog\Models\ProductOptionGroup;

  class PublicCatalogPresenter
  {
      public function present(Company $company, Branch $branch, Catalog $catalog): array
      {
          $categories = CatalogCategory::withoutTenancy()
              ->where('catalog_id', $catalog->id)
              ->where('is_active', true)
              ->orderBy('sort_order')
              ->orderBy('name')
              ->with(['products' => fn ($q) => $q->withoutTenancy()
                  ->where('is_active', true)
                  ->orderBy('sort_order')
                  ->orderBy('name')
                  ->with(['optionGroups' => fn ($q) => $q->withoutTenancy()
                      ->orderBy('sort_order')
                      ->with(['options' => fn ($q) => $q->withoutTenancy()
                          ->where('is_active', true)
                          ->orderBy('sort_order')])])])
              ->get()
              ->filter(fn (CatalogCategory $category): bool => $category->products->isNotEmpty())
              ->values();

          return [
              'store' => [
                  'name' => $company->name,
                  'logo_url' => null,
                  'currency' => $company->currency ?: 'USD',
              ],
              'branch' => [
                  'uuid' => $branch->uuid,
                  'name' => $branch->name,
                  'city' => $branch->cityRecord?->name,
                  'whatsapp' => $branch->whatsapp_number,
              ],
              'categories' => $categories->map(fn (CatalogCategory $category): array => [
                  'id' => $category->id,
                  'name' => $category->name,
                  'description' => $category->description,
                  'products' => $category->products->map(fn (Product $product): array => [
                      'id' => $product->id,
                      'uuid' => $product->uuid,
                      'name' => $product->name,
                      'description' => $product->description,
                      'price_cents' => $this->cents($product->price),
                      'image_url' => $product->imageUrl(),
                      'is_available' => $product->is_available,
                      'option_groups' => $product->optionGroups
                          ->filter(fn (ProductOptionGroup $group): bool => $group->options->isNotEmpty())
                          ->map(fn (ProductOptionGroup $group): array => [
                              'id' => $group->id,
                              'name' => $group->name,
                              'selection_type' => $group->selection_type,
                              'is_required' => $group->is_required,
                              'max_selections' => $group->max_selections,
                              'options' => $group->options->map(fn (ProductOption $option): array => [
                                  'id' => $option->id,
                                  'name' => $option->name,
                                  'price_cents' => $this->cents($option->price),
                              ])->values()->all(),
                          ])->values()->all(),
                  ])->values()->all(),
              ])->all(),
          ];
      }

      private function cents(string|int|float|null $amount): int
      {
          return (int) round(((float) $amount) * 100);
      }
  }
  ```
  Reglas que implementa este código (no las cambies):
  - Solo categorías visibles con al menos 1 producto visible.
  - Productos agotados (`is_available = false`) **sí** se envían (la vista los muestra como "Agotado").
  - Grupos de opciones sin opciones activas **no** se envían.
  - Precios en centavos enteros.
  - No se envían `company_id`, usuarios, emails ni teléfonos (solo el WhatsApp de la sucursal, que es necesario para el enlace).

### 7.4 Controlador
- [x] Crear `app/Http/Controllers/PublicCatalog/PublicCatalogController.php`:
  ```php
  namespace App\Http\Controllers\PublicCatalog;

  use App\Core\Branches\Models\Branch;
  use App\Core\Companies\Models\Company;
  use App\Domain\Catalog\Services\CatalogResolver;
  use App\Domain\Catalog\Services\PublicCatalogPresenter;
  use App\Http\Controllers\Controller;
  use Illuminate\Http\Response;

  class PublicCatalogController extends Controller
  {
      public function show(string $companySlug, string $branchUuid, CatalogResolver $resolver, PublicCatalogPresenter $presenter): Response
      {
          $company = Company::query()
              ->where('slug', $companySlug)
              ->where('is_active', true)
              ->where('status', 'active')
              ->first();                               // SoftDeletes ya excluye las borradas

          $branch = $company
              ? Branch::withoutTenancy()
                  ->where('company_id', $company->id)
                  ->where('uuid', $branchUuid)
                  ->first()
              : null;

          $catalog = ($branch && $branch->isPubliclyAvailable()) ? $resolver->forBranch($branch) : null;

          if (! $catalog) {
              return $this->unavailable();
          }

          return response()
              ->view('public-catalog.show', ['payload' => $presenter->present($company, $branch, $catalog)])
              ->header('X-Robots-Tag', 'noindex, nofollow');
      }

      public function invalid(): Response
      {
          return $this->unavailable();
      }

      private function unavailable(): Response
      {
          return response()
              ->view('public-catalog.unavailable', [], 404)
              ->header('X-Robots-Tag', 'noindex, nofollow');
      }
  }
  ```
  > El mismo mensaje y código 404 para **todos** los casos de error (D2). No usar `firstOrFail()` (mostraría la página 404 genérica de Laravel).

### 7.5 Layout público
- [x] Crear `resources/views/layouts/public.blade.php`:
  ```blade
  <!DOCTYPE html>
  <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
      <head>
          <meta charset="utf-8">
          <meta name="viewport" content="width=device-width, initial-scale=1">
          <meta name="robots" content="noindex, nofollow">
          <title>@yield('title', config('app.name'))</title>
          <link rel="preconnect" href="https://fonts.bunny.net">
          <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
          @vite(['resources/css/app.css', 'resources/js/app.js'])
      </head>
      <body class="bg-gray-50 font-sans text-gray-900 antialiased">
          <div class="mx-auto min-h-screen max-w-2xl">
              @yield('content')
          </div>
      </body>
  </html>
  ```
- [x] En `resources/css/app.css` agregar al final: `[x-cloak] { display: none !important; }`

### 7.6 Vista de error `resources/views/public-catalog/unavailable.blade.php`
- [x] Crear:
  ```blade
  @extends('layouts.public')

  @section('title', 'Enlace no disponible')

  @section('content')
      <div class="flex min-h-screen flex-col items-center justify-center px-6 text-center">
          <h1 class="text-xl font-semibold text-gray-900">Este enlace no es válido o el catálogo no está disponible en este momento.</h1>
          <p class="mt-3 text-gray-600">Por favor, pide a la tienda un enlace actualizado por WhatsApp.</p>
      </div>
  @endsection
  ```

### 7.7 Vista del catálogo `resources/views/public-catalog/show.blade.php`
- [x] Crear con este contenido (todo dato de BD se pinta con `x-text`, **nunca** `x-html` ni `{!! !!}`):
  ```blade
  @extends('layouts.public')

  @section('title', $payload['store']['name'])

  @section('content')
  <div x-data="publicCatalog(@js($payload))" x-cloak class="pb-28">

      {{-- Cabecera --}}
      <header class="bg-white px-4 py-5 shadow-sm">
          <h1 class="text-xl font-bold text-gray-900" x-text="payload.store.name"></h1>
          <p class="text-sm text-gray-600" x-text="payload.branch.name + (payload.branch.city ? ' — ' + payload.branch.city : '')"></p>
      </header>

      {{-- Catálogo vacío --}}
      <p x-show="payload.categories.length === 0" class="px-4 py-10 text-center text-gray-600">Este catálogo aún no tiene productos.</p>

      {{-- Navegación de categorías --}}
      <nav x-show="payload.categories.length > 0" class="sticky top-0 z-10 flex gap-2 overflow-x-auto bg-gray-50 px-4 py-3" aria-label="Categorías">
          <template x-for="category in payload.categories" :key="category.id">
              <a :href="'#cat-' + category.id" class="shrink-0 rounded-full bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm" x-text="category.name"></a>
          </template>
      </nav>

      {{-- Productos --}}
      <main class="space-y-8 px-4 py-4">
          <template x-for="category in payload.categories" :key="category.id">
              <section :id="'cat-' + category.id" class="scroll-mt-16">
                  <h2 class="mb-3 text-lg font-semibold text-gray-900" x-text="category.name"></h2>
                  <div class="space-y-3">
                      <template x-for="product in category.products" :key="product.id">
                          <article class="flex gap-3 rounded-lg bg-white p-3 shadow-sm">
                              <template x-if="product.image_url">
                                  <img :src="product.image_url" :alt="product.name" loading="lazy" class="h-20 w-20 shrink-0 rounded-md object-cover">
                              </template>
                              <div class="flex min-w-0 flex-1 flex-col">
                                  <h3 class="font-medium text-gray-900" x-text="product.name"></h3>
                                  <p class="line-clamp-2 text-sm text-gray-600" x-text="product.description || ''"></p>
                                  <div class="mt-auto flex items-center justify-between pt-2">
                                      <span class="font-semibold text-gray-900" x-text="money(product.price_cents)"></span>
                                      <span x-show="!product.is_available" class="rounded bg-gray-200 px-2 py-1 text-xs font-semibold text-gray-700">Agotado</span>
                                      <button x-show="product.is_available" type="button" @click="openProduct(product)" class="min-h-[44px] rounded-md bg-green-600 px-4 text-sm font-semibold text-white">Agregar</button>
                                  </div>
                              </div>
                          </article>
                      </template>
                  </div>
              </section>
          </template>
      </main>

      {{-- Modal de producto --}}
      <div x-show="modalProduct !== null" class="fixed inset-0 z-30 flex items-end justify-center bg-black/50 sm:items-center" role="dialog" aria-modal="true" @click.self="closeModal()" @keydown.escape.window="closeModal()">
          <template x-if="modalProduct !== null">
              <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-t-2xl bg-white p-5 sm:rounded-2xl">
                  <div class="flex items-start justify-between gap-3">
                      <h2 class="text-lg font-semibold" x-text="modalProduct.name"></h2>
                      <button type="button" @click="closeModal()" aria-label="Cerrar" class="min-h-[44px] min-w-[44px] text-2xl leading-none text-gray-500">&times;</button>
                  </div>
                  <p class="mt-1 text-sm text-gray-600" x-text="modalProduct.description || ''"></p>

                  <template x-for="group in modalProduct.option_groups" :key="group.id">
                      <fieldset class="mt-5">
                          <legend class="font-medium text-gray-900">
                              <span x-text="group.name"></span>
                              <span x-show="group.is_required" class="text-sm text-red-600">(obligatorio)</span>
                              <span x-show="group.selection_type === 'multiple' && group.max_selections" class="text-sm text-gray-500" x-text="'(máx. ' + group.max_selections + ')'"></span>
                          </legend>
                          <template x-for="option in group.options" :key="option.id">
                              <label class="mt-2 flex min-h-[44px] items-center justify-between gap-3 rounded-md border border-gray-200 px-3">
                                  <span class="flex items-center gap-3">
                                      <input :type="group.selection_type === 'single' ? 'radio' : 'checkbox'"
                                             :name="'group-' + group.id"
                                             :checked="isSelected(group, option.id)"
                                             :disabled="isOptionDisabled(group, option.id)"
                                             @change="toggleOption(group, option.id)">
                                      <span x-text="option.name"></span>
                                  </span>
                                  <span x-show="option.price_cents > 0" class="text-sm text-gray-600" x-text="'+' + money(option.price_cents)"></span>
                              </label>
                          </template>
                      </fieldset>
                  </template>

                  <div class="mt-5 flex items-center justify-between">
                      <span class="font-medium">Cantidad</span>
                      <div class="flex items-center gap-3">
                          <button type="button" @click="modalQty = Math.max(1, modalQty - 1)" aria-label="Disminuir cantidad" class="h-11 w-11 rounded-full border border-gray-300 text-xl">−</button>
                          <span class="w-8 text-center font-semibold" x-text="modalQty"></span>
                          <button type="button" @click="modalQty = Math.min(MAX_QTY, modalQty + 1)" aria-label="Aumentar cantidad" class="h-11 w-11 rounded-full border border-gray-300 text-xl">+</button>
                      </div>
                  </div>

                  <label class="mt-5 block">
                      <span class="text-sm font-medium text-gray-700">Nota (opcional)</span>
                      <input type="text" x-model="modalNote" maxlength="140" placeholder="Ej: sin cebolla" class="mt-1 w-full rounded-md border-gray-300">
                  </label>

                  <button type="button" @click="addToCart()" :disabled="!canAddModal()" class="mt-6 min-h-[48px] w-full rounded-md bg-green-600 font-semibold text-white disabled:cursor-not-allowed disabled:bg-gray-300">
                      <span x-text="'Agregar · ' + money(modalLineCents())"></span>
                  </button>
              </div>
          </template>
      </div>

      {{-- Barra inferior --}}
      <div x-show="cart.length > 0 && !cartOpen" class="fixed inset-x-0 bottom-0 z-20 bg-white p-3 shadow-[0_-2px_8px_rgba(0,0,0,0.1)]">
          <button type="button" @click="cartOpen = true" class="mx-auto flex min-h-[48px] w-full max-w-2xl items-center justify-between rounded-md bg-green-600 px-4 font-semibold text-white">
              <span x-text="'Ver pedido (' + itemCount + ')'"></span>
              <span x-text="money(totalCents)"></span>
          </button>
      </div>

      {{-- Panel del pedido --}}
      <div x-show="cartOpen" class="fixed inset-0 z-30 flex items-end justify-center bg-black/50 sm:items-center" role="dialog" aria-modal="true" @click.self="cartOpen = false" @keydown.escape.window="cartOpen = false">
          <div class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-t-2xl bg-white p-5 sm:rounded-2xl">
              <div class="flex items-center justify-between">
                  <h2 class="text-lg font-semibold">Tu pedido</h2>
                  <button type="button" @click="cartOpen = false" aria-label="Cerrar" class="min-h-[44px] min-w-[44px] text-2xl leading-none text-gray-500">&times;</button>
              </div>

              <template x-for="item in cart" :key="item.key">
                  <div class="border-b border-gray-200 py-3">
                      <div class="flex justify-between gap-3">
                          <p class="font-medium" x-text="productOf(item).name"></p>
                          <p class="font-semibold" x-text="money(lineCents(item))"></p>
                      </div>
                      <template x-for="line in optionLines(item)" :key="line">
                          <p class="text-sm text-gray-600" x-text="line"></p>
                      </template>
                      <p x-show="item.note" class="text-sm italic text-gray-600" x-text="'Nota: ' + item.note"></p>
                      <div class="mt-2 flex items-center gap-3">
                          <button type="button" @click="changeQty(item, -1)" aria-label="Disminuir cantidad" class="h-11 w-11 rounded-full border border-gray-300 text-xl">−</button>
                          <span class="w-8 text-center font-semibold" x-text="item.qty"></span>
                          <button type="button" @click="changeQty(item, 1)" aria-label="Aumentar cantidad" class="h-11 w-11 rounded-full border border-gray-300 text-xl">+</button>
                          <button type="button" @click="removeItem(item)" class="ml-auto min-h-[44px] text-sm font-medium text-red-600">Eliminar</button>
                      </div>
                  </div>
              </template>

              <label class="mt-4 block">
                  <span class="text-sm font-medium text-gray-700">Nota general (opcional)</span>
                  <textarea x-model="generalNote" @input="persist()" maxlength="300" rows="2" class="mt-1 w-full rounded-md border-gray-300"></textarea>
              </label>

              <div class="mt-4 flex items-center justify-between text-lg font-semibold">
                  <span>Total estimado</span>
                  <span x-text="money(totalCents)"></span>
              </div>

              <button type="button" @click="send()" class="mt-5 min-h-[52px] w-full rounded-md bg-green-600 text-lg font-semibold text-white">Enviar pedido por WhatsApp</button>

              <p x-show="sent" class="mt-3 text-center text-sm text-gray-600">¿Ya enviaste tu pedido? Puedes vaciarlo abajo.</p>

              <button type="button" @click="clearCart()" class="mt-3 min-h-[44px] w-full rounded-md border border-gray-300 font-medium text-gray-700">Vaciar pedido</button>
          </div>
      </div>
  </div>
  @endsection
  ```

### 7.8 Componente Alpine `resources/js/public-catalog.js`
- [x] Crear `resources/js/public-catalog.js` con este contenido exacto:
  ```js
  const MAX_QTY = 99;
  const ITEM_NOTE_MAX = 140;
  const GENERAL_NOTE_MAX = 300;

  export default (payload) => ({
      payload,
      MAX_QTY,
      index: {},
      cart: [],
      generalNote: '',
      modalProduct: null,
      modalSelections: {},
      modalQty: 1,
      modalNote: '',
      cartOpen: false,
      sent: false,

      init() {
          this.payload.categories.forEach((category) => {
              category.products.forEach((product) => {
                  this.index[product.id] = product;
              });
          });
          this.restore();
      },

      get storageKey() {
          return `catalog-cart:${this.payload.branch.uuid}`;
      },

      get itemCount() {
          return this.cart.reduce((sum, item) => sum + item.qty, 0);
      },

      get totalCents() {
          return this.cart.reduce((sum, item) => sum + this.lineCents(item), 0);
      },

      // ---------- Persistencia ----------
      restore() {
          try {
              const saved = JSON.parse(localStorage.getItem(this.storageKey) || '{}');
              this.generalNote = typeof saved.generalNote === 'string' ? saved.generalNote.slice(0, GENERAL_NOTE_MAX) : '';
              this.cart = (Array.isArray(saved.cart) ? saved.cart : []).filter((item) => this.isValidItem(item));
          } catch (error) {
              this.cart = [];
              this.generalNote = '';
          }
          this.persist();
      },

      persist() {
          localStorage.setItem(this.storageKey, JSON.stringify({ cart: this.cart, generalNote: this.generalNote }));
      },

      isValidItem(item) {
          const product = this.index[item?.productId];
          if (!product || !product.is_available) return false;
          if (!Number.isInteger(item.qty) || item.qty < 1 || item.qty > MAX_QTY) return false;
          if (!Array.isArray(item.optionIds) || typeof item.key !== 'string') return false;
          const validIds = product.option_groups.flatMap((group) => group.options.map((option) => option.id));
          if (!item.optionIds.every((id) => validIds.includes(id))) return false;
          return this.selectionsAreValid(product, this.groupSelections(product, item.optionIds));
      },

      // ---------- Reglas de opciones ----------
      groupSelections(product, optionIds) {
          const selections = {};
          product.option_groups.forEach((group) => {
              selections[group.id] = group.options.filter((o) => optionIds.includes(o.id)).map((o) => o.id);
          });
          return selections;
      },

      selectionsAreValid(product, selections) {
          return product.option_groups.every((group) => {
              const count = (selections[group.id] || []).length;
              if (group.is_required && count < 1) return false;
              if (group.selection_type === 'single' && count > 1) return false;
              if (group.selection_type === 'multiple' && group.max_selections && count > group.max_selections) return false;
              return true;
          });
      },

      // ---------- Modal ----------
      openProduct(product) {
          if (!product.is_available) return;
          this.modalSelections = {};
          product.option_groups.forEach((group) => {
              this.modalSelections[group.id] = [];
          });
          this.modalQty = 1;
          this.modalNote = '';
          this.modalProduct = product;
      },

      closeModal() {
          this.modalProduct = null;
      },

      isSelected(group, optionId) {
          return (this.modalSelections[group.id] || []).includes(optionId);
      },

      isOptionDisabled(group, optionId) {
          if (group.selection_type !== 'multiple' || !group.max_selections) return false;
          const current = this.modalSelections[group.id] || [];
          return current.length >= group.max_selections && !current.includes(optionId);
      },

      toggleOption(group, optionId) {
          const current = this.modalSelections[group.id] || [];
          if (group.selection_type === 'single') {
              this.modalSelections[group.id] = [optionId];
              return;
          }
          if (current.includes(optionId)) {
              this.modalSelections[group.id] = current.filter((id) => id !== optionId);
              return;
          }
          if (group.max_selections && current.length >= group.max_selections) return;
          this.modalSelections[group.id] = [...current, optionId];
      },

      modalOptionIds() {
          return Object.values(this.modalSelections).flat().sort((a, b) => a - b);
      },

      canAddModal() {
          return this.modalProduct !== null && this.selectionsAreValid(this.modalProduct, this.modalSelections);
      },

      modalLineCents() {
          if (this.modalProduct === null) return 0;
          return this.unitCents(this.modalProduct, this.modalOptionIds()) * this.modalQty;
      },

      // ---------- Carrito ----------
      cleanNote(text, max) {
          return String(text || '').replace(/\s+/g, ' ').trim().slice(0, max);
      },

      addToCart() {
          if (!this.canAddModal()) return;
          const optionIds = this.modalOptionIds();
          const note = this.cleanNote(this.modalNote, ITEM_NOTE_MAX);
          const key = `${this.modalProduct.id}|${optionIds.join(',')}|${note}`;
          const existing = this.cart.find((item) => item.key === key);
          if (existing) {
              existing.qty = Math.min(MAX_QTY, existing.qty + this.modalQty);
          } else {
              this.cart.push({ key, productId: this.modalProduct.id, qty: this.modalQty, optionIds, note });
          }
          this.sent = false;
          this.persist();
          this.closeModal();
      },

      changeQty(item, delta) {
          const qty = item.qty + delta;
          if (qty < 1) {
              this.removeItem(item);
              return;
          }
          item.qty = Math.min(MAX_QTY, qty);
          this.persist();
      },

      removeItem(item) {
          this.cart = this.cart.filter((entry) => entry.key !== item.key);
          if (this.cart.length === 0) this.cartOpen = false;
          this.persist();
      },

      clearCart() {
          if (!window.confirm('¿Vaciar el pedido?')) return;
          this.cart = [];
          this.generalNote = '';
          this.sent = false;
          this.cartOpen = false;
          this.persist();
      },

      // ---------- Precios ----------
      productOf(item) {
          return this.index[item.productId];
      },

      unitCents(product, optionIds) {
          const extras = product.option_groups
              .flatMap((group) => group.options)
              .filter((option) => optionIds.includes(option.id))
              .reduce((sum, option) => sum + option.price_cents, 0);
          return product.price_cents + extras;
      },

      lineCents(item) {
          return this.unitCents(this.productOf(item), item.optionIds) * item.qty;
      },

      money(cents) {
          try {
              return new Intl.NumberFormat('es-VE', { style: 'currency', currency: this.payload.store.currency }).format(cents / 100);
          } catch (error) {
              return (cents / 100).toFixed(2);
          }
      },

      optionLines(item) {
          const product = this.productOf(item);
          return product.option_groups
              .map((group) => {
                  const chosen = group.options.filter((option) => item.optionIds.includes(option.id));
                  if (chosen.length === 0) return null;
                  const labels = chosen.map((option) => (option.price_cents > 0 ? `${option.name} (+${this.money(option.price_cents)})` : option.name));
                  return `${group.name}: ${labels.join(', ')}`;
              })
              .filter((line) => line !== null);
      },

      // ---------- WhatsApp ----------
      buildMessage() {
          const { store, branch } = this.payload;
          const lines = [
              `*Nuevo pedido* — ${store.name}`,
              `Sucursal: ${branch.name}${branch.city ? ` — ${branch.city}` : ''}`,
              '',
          ];
          this.cart.forEach((item, position) => {
              const product = this.productOf(item);
              lines.push(`${position + 1}) ${item.qty} x ${product.name} — ${this.money(this.lineCents(item))}`);
              this.optionLines(item).forEach((line) => lines.push(`   • ${line}`));
              if (item.note) lines.push(`   Nota: ${item.note}`);
          });
          lines.push('', `*Total estimado: ${this.money(this.totalCents)}*`);
          const generalNote = this.cleanNote(this.generalNote, GENERAL_NOTE_MAX);
          if (generalNote) lines.push('', `Nota general: ${generalNote}`);
          lines.push('', '_Pedido generado desde el catálogo web. La tienda confirmará el monto final._');
          return lines.join('\n');
      },

      whatsappUrl() {
          return `https://wa.me/${this.payload.branch.whatsapp}?text=${encodeURIComponent(this.buildMessage())}`;
      },

      send() {
          if (this.cart.length === 0) return;
          window.open(this.whatsappUrl(), '_blank', 'noopener');
          this.sent = true;
      },
  });
  ```
  Reglas que implementa (no las cambies): precios siempre recalculados desde el payload (nunca desde `localStorage`); un carrito por sucursal; ítems idénticos (mismo producto, opciones y nota) se agrupan sumando cantidad; el carrito **no** se vacía solo al enviar.
- [x] En `resources/js/app.js` dejarlo así (el import y `Alpine.data` van **antes** de `Alpine.start()`):
  ```js
  import './bootstrap';

  import Alpine from 'alpinejs';
  import publicCatalog from './public-catalog';

  window.Alpine = Alpine;

  Alpine.data('publicCatalog', publicCatalog);

  Alpine.start();
  ```
- [x] Ejecutar `docker compose exec delivery-app npm run build` (debe terminar sin errores).

### 7.9 Imágenes públicas
- [x] Ejecutar `docker compose exec delivery-app php artisan storage:link` (crea `public/storage`). Comprobar que una imagen subida en la Fase 6 abre en `http://localhost:8000/storage/products/<archivo>`.
- [x] No tocar `nginx/default.conf`: ya sirve `/storage` (R11).

### 7.10 Tests — `tests/Feature/Catalog/PublicCatalogTest.php`
`setUp()`: `parent::setUp(); $this->withoutVite();` (R7). Datos base en un helper privado `makeStore(): array` que devuelve `[$company, $branch, $catalog, $category, $activeProduct, $inactiveProduct]`: tienda activa (`slug = 'tienda-a'`), sucursal activa con `whatsapp_number = '584141111111'`, catálogo por defecto (`$company->defaultCatalog()->withoutTenancy()->first()`), categoría visible, producto activo `name = 'Producto Visible'`, producto `is_active = false` `name = 'Producto Oculto'`.

> Usar **solo nombres ASCII** (sin tildes) en los datos de test: la vista entrega el payload codificado con `@js`, que escapa los caracteres no ASCII, y `assertSee` no los encontraría.
> Para revisar el contenido, usar `->assertViewHas('payload', fn (array $payload): bool => ...)` en vez de buscar texto en el HTML (los productos se pintan con Alpine en el navegador).

- [x] `test_valid_link_shows_catalog` ⇒ `get("/c/tienda-a/{$branch->uuid}")` → `assertOk()`, `assertHeader('X-Robots-Tag', 'noindex, nofollow')`, `assertViewHas('payload')` con: `store.name` correcto, `branch.whatsapp === '584141111111'`, contiene 'Producto Visible' y **no** contiene 'Producto Oculto'.
- [x] `test_link_without_branch_uuid_shows_error` (`/c/tienda-a`) ⇒ `assertNotFound()` + `assertSee('Este enlace no es válido', false)`.
- [x] `test_link_with_malformed_uuid_shows_error` (`/c/tienda-a/123`) ⇒ 404.
- [x] `test_link_with_extra_segments_shows_error` (`/c/tienda-a/{uuid}/extra`) ⇒ 404.
- [x] `test_branch_of_another_store_shows_error` (slug de A + uuid de una sucursal de B) ⇒ 404.
- [x] `test_unknown_uuid_shows_error` (UUID válido aleatorio `Str::uuid()`) ⇒ 404.
- [x] `test_inactive_branch_shows_error` (`status = 'inactive'`) ⇒ 404.
- [x] `test_branch_without_whatsapp_shows_error` (`whatsapp_number = null`) ⇒ 404.
- [x] `test_inactive_company_shows_error` (`is_active = false`) ⇒ 404.
- [x] `test_company_with_inactive_status_shows_error` (`status = 'inactive'`) ⇒ 404.
- [x] `test_soft_deleted_company_shows_error` (`$company->delete()`) ⇒ 404.
- [x] `test_inactive_catalog_shows_error` (catálogo por defecto `is_active = false`) ⇒ 404.
- [x] `test_unknown_slug_shows_error` ⇒ 404.
- [x] `test_catalog_is_visible_when_logged_in_as_owner_of_another_store` (R1): `actingAs(makeStoreOwner($otraTienda))` y abrir el enlace de la tienda A ⇒ 200 y el payload contiene 'Producto Visible'.
- [x] `test_unavailable_product_is_sent_as_sold_out` ⇒ producto con `is_available = false` aparece en el payload con `is_available === false`.
- [x] `test_empty_option_groups_are_not_sent` ⇒ grupo cuyas opciones están todas `is_active = false` no aparece en el payload.
- [x] `test_payload_does_not_expose_company_id` ⇒ `assertDontSee('company_id', false)`.
- [x] `test_presenter_returns_prices_in_cents` ⇒ producto `price = 5.50` ⇒ `price_cents === 550`; opción `price = 1.25` ⇒ `price_cents === 125`.
- [x] `test_rate_limit_returns_429_after_60_requests` ⇒ 60 GET seguidos al enlace válido responden 200 y el 61.º responde 429.

### 7.11 Pruebas manuales (obligatorias: el JS no tiene tests automáticos)
Abrir el enlace desde la acción "Ver catálogo" del panel (7.12). Probar en Chrome con la vista móvil (DevTools → 390px). La prueba en un teléfono real con WhatsApp la hace el propietario en la tarea 9.6.
- [ ] Se ven categorías, productos, imágenes y precios en la moneda de la tienda.
- [ ] Producto agotado: muestra "Agotado" y no tiene botón "Agregar".
- [ ] Grupo obligatorio: el botón "Agregar" se habilita solo al elegir una opción.
- [ ] Grupo múltiple con máximo 3: al marcar 3, las demás opciones quedan deshabilitadas.
- [ ] Precio de línea y total correctos: 3 × producto de 5,50 con extra de 1,25 ⇒ 20,25.
- [ ] Agregar el mismo producto con las mismas opciones y nota suma cantidad; con opciones distintas crea otra línea.
- [ ] Recargar la página conserva el pedido y la nota general; el enlace de otra sucursal tiene su propio carrito.
- [ ] "Enviar pedido por WhatsApp" abre `https://wa.me/<número de la sucursal>?text=...` con el texto en el formato del `buildMessage()` de 7.8 y las tildes bien mostradas.
- [ ] `/c/<slug>` sin UUID, UUID cambiado a mano y tienda desactivada ⇒ página de error.
- [ ] Un producto llamado `<script>alert(1)</script>` se muestra como texto y no ejecuta nada.

### 7.12 Enlace del catálogo en el panel de sucursales
- [x] En `app/Filament/Resources/Branches/Schemas/BranchForm.php` agregar al final de `components([...])`:
  ```php
  TextInput::make('public_catalog_url')
      ->label('Enlace del catálogo para esta sucursal')
      ->helperText('Comparte este enlace con tus clientes por WhatsApp.')
      ->formatStateUsing(fn (?Branch $record): ?string => $record?->publicCatalogUrl())
      ->disabled()
      ->dehydrated(false)
      ->copyable()
      ->visibleOn('edit')
      ->columnSpanFull(),
  ```
  (`use App\Core\Branches\Models\Branch;`)
- [x] En `app/Filament/Resources/Branches/Tables/BranchesTable.php`, en `recordActions([...])` agregar antes de `EditAction::make()`:
  ```php
  Action::make('openCatalog')
      ->label('Ver catálogo')
      ->icon(Heroicon::OutlinedArrowTopRightOnSquare)
      ->url(fn (Branch $record): ?string => $record->publicCatalogUrl())
      ->openUrlInNewTab()
      ->visible(fn (Branch $record): bool => $record->isPubliclyAvailable()),
  ```
  (`use Filament\Actions\Action; use Filament\Support\Icons\Heroicon; use App\Core\Branches\Models\Branch;`)
- [x] Test en `PublicCatalogTest`: `test_branch_public_catalog_url_matches_route` ⇒ `$branch->publicCatalogUrl()` termina en `"/c/tienda-a/{$branch->uuid}"`.

### Verificación Fase 7
- [x] Implementado — [x] Probado (tests) — [ ] Probado (manual 7.11)

---

## FASE 8 — Datos de demostración

> Los seeders corren **sin usuario logueado**: por eso `Branch` y `Catalog` se crean con `company_id` explícito y todas las búsquedas usan `withoutTenancy()` (R1, R2). Todos los seeders deben ser **idempotentes** (ejecutarlos varias veces no duplica nada).

- [x] **8.1** Crear `database/seeders/CitySeeder.php`:
  ```php
  public function run(): void
  {
      $cities = [
          ['name' => 'El Tigre', 'slug' => 'el-tigre', 'state' => 'Anzoátegui'],
          ['name' => 'Puerto La Cruz', 'slug' => 'puerto-la-cruz', 'state' => 'Anzoátegui'],
          ['name' => 'Barcelona', 'slug' => 'barcelona', 'state' => 'Anzoátegui'],
          ['name' => 'Maturín', 'slug' => 'maturin', 'state' => 'Monagas'],
      ];

      foreach ($cities as $city) {
          City::query()->firstOrCreate(['slug' => $city['slug']], [...$city, 'country' => 'VE', 'is_active' => true]);
      }
  }
  ```
- [x] **8.2** Crear `database/seeders/DemoStoreSeeder.php`. Estructura del `run()`:
  1. Tienda:
     ```php
     $company = Company::query()->firstOrCreate(['slug' => 'burger-demo'], [
         'name' => 'Burger Demo', 'currency' => 'USD', 'country' => 'VE',
         'timezone' => 'America/Caracas', 'language' => 'es', 'status' => 'active', 'is_active' => true,
     ]);
     $catalog = app(EnsureDefaultCatalogAction::class)->execute($company);
     $elTigre = City::query()->where('slug', 'el-tigre')->firstOrFail();
     ```
  2. Sucursales (con `Branch::withoutTenancy()->firstOrCreate(['company_id' => $company->id, 'name' => ...], [...])`):
     - "Centro": `city_id = $elTigre->id`, `address = 'Av. Francisco de Miranda'`, `whatsapp_number = '584140000001'`, `is_main = true`, `status = 'active'`, `timezone = 'America/Caracas'`, `country = 'VE'`.
     - "Pueblo Nuevo": igual pero `address = 'Calle Bolívar'`, `whatsapp_number = '584140000002'`, `is_main = false`.
  3. Categorías (`CatalogCategory::withoutTenancy()->firstOrCreate(['catalog_id' => $catalog->id, 'name' => ...], ['sort_order' => n, 'is_active' => true])`): "Hamburguesas" (1), "Perros calientes" (2), "Bebidas" (3).
  4. Productos (`Product::withoutTenancy()->firstOrCreate(['catalog_id' => $catalog->id, 'name' => ...], [...])`), grupos (`ProductOptionGroup::withoutTenancy()->firstOrCreate(['product_id' => ..., 'name' => ...], [...])`) y opciones (`ProductOption::withoutTenancy()->firstOrCreate(['product_option_group_id' => ..., 'name' => ...], [...])`):

     | Categoría | Producto | Precio | Disponible | Grupos y opciones |
     |---|---|---|---|---|
     | Hamburguesas | Hamburguesa Clásica | 5.50 | sí | "Tamaño" (single, obligatorio): Normal 0, Doble 2.00 · "Extras" (multiple, máx 3, no obligatorio): Queso 0.50, Tocineta 1.00, Huevo 0.75 |
     | Hamburguesas | Hamburguesa BBQ | 7.00 | **no** (agotado) | — |
     | Perros calientes | Perro Especial | 3.00 | sí | "Salsas" (multiple, sin máximo, no obligatorio): Ketchup 0, Mostaza 0, Mayonesa 0 |
     | Bebidas | Refresco 1.5L | 2.50 | sí | "Sabor" (single, obligatorio): Cola 0, Naranja 0 |

  5. Usuario dueño:
     ```php
     $owner = User::query()->firstOrCreate(['email' => 'owner@example.com'], [
         'name' => 'Dueño Burger Demo',
         'password' => Hash::make('password'),
         'email_verified_at' => now(),
         'company_id' => $company->id,
         'current_company_id' => $company->id,
     ]);
     $company->users()->syncWithoutDetaching([$owner->id => ['role' => 'owner']]);
     $owner->syncRoles(['store_owner']);
     ```
  6. Imprimir los enlaces:
     ```php
     Branch::withoutTenancy()->where('company_id', $company->id)->orderBy('id')->get()
         ->each(fn (Branch $branch) => $this->command?->info("{$branch->name}: {$branch->publicCatalogUrl()}"));
     ```
- [x] **8.3** En `database/seeders/DatabaseSeeder.php`, dejar el array de `$this->call([...])` en este orden exacto:
  ```php
  PermissionSeeder::class,
  RoleSeeder::class,
  CitySeeder::class,
  CompanySeeder::class,
  BranchSeeder::class,
  UserSeeder::class,
  SettingSeeder::class,
  PlanSeeder::class,
  ModuleSeeder::class,
  DemoStoreSeeder::class,
  ```
- [x] **8.4** Ejecutar solo los seeders necesarios (no borran datos):
  ```bash
  docker compose exec delivery-app php artisan db:seed --class=RoleSeeder
  docker compose exec delivery-app php artisan db:seed --class=CitySeeder
  docker compose exec delivery-app php artisan db:seed --class=DemoStoreSeeder
  ```
  La salida del último muestra los 2 enlaces públicos. Copiarlos en la tarea 9.6.
- [x] **8.5** Test `tests/Feature/Catalog/DemoStoreSeederTest.php`:
  - `setUp()`: `parent::setUp(); Role::findOrCreate('store_owner', 'web');`
  - `test_demo_seeder_is_idempotent`: `$this->seed(CitySeeder::class); $this->seed(DemoStoreSeeder::class); $this->seed(DemoStoreSeeder::class);` y comprobar (con `withoutTenancy()` donde aplique): 1 tienda `burger-demo`, 1 catálogo por defecto, 2 sucursales, 3 categorías, 4 productos, 4 grupos de opciones, 10 opciones, 1 usuario `owner@example.com` con rol `store_owner` y fila en `company_user`.
  - `test_demo_links_open_the_catalog`: después de sembrar, `$this->withoutVite()` y `get()` a la URL de cada sucursal ⇒ 200.

### Verificación Fase 8
- [x] Implementado — [x] Probado
- [ ] Manual: entrar a `/admin` con `owner@example.com` / `password` ⇒ solo ve "Burger Demo", sus 2 sucursales, sus categorías y productos.

---

## FASE 9 — Cierre

- [x] **9.1** Ejecutar `docker compose exec delivery-app vendor/bin/pint app database routes tests` (corrige el formato automáticamente).
- [x] **9.2** Ejecutar `docker compose exec delivery-app php artisan test`: todo en verde. Anotar número final: `____` (debe ser 74 + todos los tests nuevos de este archivo). Resultado: `134` (134 passed, 445 assertions).
- [x] **9.3** Ejecutar `docker compose exec delivery-app npm run build`: sin errores.
- [ ] **9.4** Revisión de seguridad (marcar cada una revisando el código):
  - [x] `PublicCatalogPresenter`, `PublicCatalogController` y `CatalogResolver` usan `withoutTenancy()` en todas las consultas a modelos `TenantModel` y filtran por `company_id`/`catalog_id`.
  - [x] `resources/views/public-catalog/*` no contiene `{!!` ni `x-html`.
  - [x] El payload público solo contiene las claves definidas en 7.3.
  - [x] Los tests de 5.6 y 6.8 pasan (aislamiento de `store_owner`).
  - [x] La ruta pública tiene `throttle:public-catalog` y responde con `X-Robots-Tag: noindex, nofollow`.
- [ ] **9.5** Documentación:
  - [x] Crear `docs/catalog.md` con estas secciones exactas: "Modelo de datos" (copiar el diagrama de 3.0), "Catálogo por defecto" (listener + comando `catalog:ensure-defaults`), "Enlace público" (formato de URL y casos de error de D2), "Formato del mensaje de WhatsApp" (copiar el formato de 7.8), "Extensión futura" (catálogos por sucursal/ciudad agregando `catalog_branch`/`catalog_city` y modificando solo `CatalogResolver`), "Roles" (`store_owner` y sus permisos de 5.2).
  - [x] En `AGENTS.md` agregar al final una sección `## Comandos y verificación` con los comandos de la sección 0.2 de este archivo y el comando `catalog:ensure-defaults`.
  - [x] En `docs/` no modificar los demás archivos.
- [x] **9.6** Entregar al propietario: los 2 enlaces de "Burger Demo" (salida de 8.4), el usuario `owner@example.com` / `password` y la lista de tareas marcadas. El propietario hace la prueba final enviando un pedido real desde su teléfono.
- [x] **9.7** Commit final: `feat: public catalog with WhatsApp ordering (phase 1)`.

---

## Fuera de alcance de esta fase (NO implementar)

- Guardar pedidos en BD, estados de pedido, notificaciones.
- Pagos, comprobantes de pago, validación de pagos.
- Ubicación del cliente / mapas / cálculo de delivery.
- Repartidores.
- Catálogos distintos por sucursal o ciudad (solo queda preparado vía `CatalogResolver`).
- Horarios de atención y cierre automático (S9).
- Logo de la tienda en el catálogo (S8).
- Doble moneda USD/Bs (S6).
- WhatsApp Business API / bots.
- Panel separado para tiendas, registro público de tiendas, planes de pago.
- Subdominios por tienda.

## Ampliación aprobada — Inicio público y 100 tiendas de prueba

Las decisiones vigentes de `AGENTS.md` prevalecen sobre las referencias históricas a varias sucursales de este plan. No borrar datos existentes.

- [ ] Limitar a una sucursal por empresa desde la aplicación, conservando la relación de múltiples sucursales para el futuro.
- [ ] Adaptar `DemoStoreSeeder` para crear una sola sucursal en instalaciones nuevas.
- [ ] Integrar el diseño aprobado en `/`, con tiendas reales, búsqueda, filtros y paginación.
- [ ] Agregar datos públicos de presentación de tiendas e imágenes con alternativa si falla la carga.
- [ ] Crear y ejecutar un seeder idempotente de al menos 100 tiendas, cada una con sucursal y catálogo navegable.
- [ ] Probar aislamiento público, enlaces, filtros, paginación, límite de sucursales e idempotencia.
- [ ] Compilar assets y verificar la navegación en escritorio y móvil.
