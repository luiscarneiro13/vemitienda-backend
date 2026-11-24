# Reglas de Negocio y Contexto del Proyecto

> Plan de implementación de la Fase 1 (tareas tachables): `TAREAS-FASE-1.md`.

> Nota: Las ideas o propuestas anteriores de Marketplace quedan descartadas. Este archivo centraliza el nuevo rumbo del proyecto.

## Reglas de Negocio

### 1. Enfoque inicial y flujo actual por WhatsApp (automatización parcial con mínima fricción)
- **Objetivo fase inicial:** Sistema web orientado a negocios de comida rápida y otros comercios que gestionan pedidos por WhatsApp.
- **Principio clave:** Generar la menor fricción posible respecto al uso actual de WhatsApp; automatizar el proceso de forma parcial (no total).
- **Flujo manual actual a optimizar:**
  1. El cliente escribe a la tienda por WhatsApp y solicita el catálogo.
  2. El cliente revisa opciones, redacta manualmente su pedido y lo envía a la tienda.
  3. La tienda calcula el monto total y se lo informa al cliente.
  4. El cliente realiza el pago y envía la captura/comprobante del pago con el monto.
  5. El cliente envía su ubicación de entrega.
  6. La tienda notifica al cliente que el pedido va en camino.
  7. El repartidor (delivery) llama al cliente y entrega el paquete.

### 2. Fase inicial: catálogo web + pedido preparado para WhatsApp (capa gratuita)
- **Plataforma:** Sistema web.
- **Super administrador:** Puede registrar tiendas. Al crear una tienda, el sistema le genera automáticamente un **catálogo único de productos** (uno por tienda).
- **Flujo del cliente:**
  1. El cliente escribe a la tienda por WhatsApp y recibe el **link del catálogo** de esa tienda.
  2. En la web, el cliente selecciona productos, cantidades, extras y demás opciones.
  3. Al finalizar, la web genera el pedido en **formato de texto** y lo prepara para enviarlo al **WhatsApp de la tienda**.
- **Alcance:** No es una automatización total; el envío y la gestión posterior siguen ocurriendo por WhatsApp.
- **Modelo comercial:** Esta funcionalidad corresponde a la **capa gratuita**. Su valor principal es reducir el tiempo que el cliente invierte en armar y redactar su pedido.

### 3. Estructura de tiendas, ciudades y sucursales
- **Decisión vigente para el lanzamiento:** cada empresa (`Company`) tendrá **una sola sucursal** (`Branch`). Esta decisión sustituye el alcance inicial de varias sucursales por empresa.
- **Evolución futura:** cuando el sistema esté en marcha, se resolverá la operación con varias sucursales, tanto dentro de una ciudad como en distintas ciudades.
- Desde el inicio se conserva la separación `Company` / `Branch` y la relación de una empresa con muchas sucursales. La limitación inicial debe aplicarse en la lógica de la aplicación, sin imponer una restricción única sobre `branches.company_id` ni trasladar los datos de sucursal a la empresa.
- La selección entre varias sucursales y la detección de ciudad por IP quedan fuera del alcance inicial; no son necesarias para resolver el destino del catálogo.
- El sistema debe diseñarse **abierto y extensible** para incorporar funcionalidades y módulos progresivamente.
- Evaluación: la estructura `Company` (= Tienda) / `Branch` (= Sucursal) del starter **sirve**, pero requiere ampliaciones (ciudades, WhatsApp por sucursal, catálogo, rutas públicas, PSR-4 `Modules\\`).

### 4. Decisiones de producto confirmadas
1. **URL pública del catálogo:** `dominio.com/c/{tienda}/{uuid_sucursal}` (slug de la tienda + UUID de la sucursal como segmentos de ruta; sin query string `?s=`; no subdominio).
   - La sucursal se identifica con un **UUID** (no ID numérico ni slug) para evitar que clientes curiosos cambien de sucursal adivinando valores.
   - El UUID debe pertenecer a la tienda del slug; si no coincide o no existe, no se debe exponer otra sucursal.
   - Hoy `branches` **no tiene columna `uuid`** (`companies` sí): agregarla vía migración nueva (`HasUuid`) y rellenar las sucursales existentes.
   - Si la URL llega **sin el UUID de sucursal** (`dominio.com/c/{tienda}`) o con un UUID inválido/ajeno a la tienda, se **muestra un error**; no hay fallback a la sucursal principal.
2. **Destino del pedido:** el WhatsApp de la **sucursal** a la que el cliente le escribió.
3. **Catálogo:** por ahora **un único catálogo por tienda**, utilizado por su única sucursal inicial y preparado para compartirse entre varias sucursales en el futuro. El diseño debe permitir en el futuro catálogos por sucursal o por ciudad **sin modificar tablas existentes, solo agregando tablas nuevas** (p. ej. tablas pivote/override).
4. **Selección de sucursal/ciudad:** el cliente **no** la elige. Es responsabilidad de la tienda: el enlace que la tienda comparte ya identifica la sucursal (y por ende la ciudad).
5. **Ciudades:** catálogo administrado exclusivamente por el **super administrador**.
6. **Pedidos en capa gratuita:** **no se persisten**; solo se genera el texto y se envía vía WhatsApp.
7. **Panel:** el dueño de la tienda gestiona su catálogo en el **mismo panel Filament** (aislado por tienda).

### 4.1 Roles (definitivo)

| Rol | Alcance |
|---|---|
| `super_admin` | Plataforma completa: registra tiendas, gestiona ciudades, sucursales (CRUD), catálogos, usuarios, roles, planes y módulos. Único que puede editar y borrar a un `store_owner`. **No puede ver ni gestionar los productos ni categorías de las tiendas (reservado exclusivamente a la tienda).** |
| `store_owner` | **Dueño de la tienda.** Administra solo su empresa (`company_id`): edita datos de la empresa (el `slug` solo lo cambia super_admin), CRUD de sus sucursales, gestiona usuarios de su empresa (`store_admin`, `user`) y su catálogo completo. **No puede ser editado ni borrado por ningún usuario de la tienda (ni por sí mismo); solo `super_admin` puede editarlo o borrarlo.** |
| `store_admin` | **Administrador de la tienda.** Cuenta con **los mismos permisos operativos** que el dueño de tienda (`store_owner`) sobre la empresa, sucursales, productos, categorías, catálogo y usuarios (`store_admin`, `user`). Puede ser editado o eliminado por el dueño u otro admin de su empresa. |
| `admin` | **Eliminado** (rol del starter). Ya no se siembra ni aparece en `canAccessPanel`. |
| `manager` | **Eliminado** (era del starter). `store_owner` y `store_admin` cubren la administración de tienda. |
| `user` | Sin permisos de panel. |

> **Reglas críticas de usuarios y autorizaciones:**
> 1. **Ningún usuario puede eliminarse a sí mismo:** Ni `super_admin`, ni `store_owner`, ni `store_admin`, ni `user` pueden borrarse a sí mismos desde el panel.
> 2. **Borrado y edición del dueño de tienda:** El dueño de la tienda (`store_owner`) **no puede ser editado ni eliminado** por el mismo dueño ni por ningún administrador de la tienda; únicamente el `super_admin` tiene autorización para editarlo o eliminarlo.
> 3. **Mismos permisos operativos:** `store_owner` y `store_admin` comparten idénticos permisos en Shield y catálogo para gestionar el día a día de su comercio.
> 4. **Aislamiento de productos y categorías:** El `super_admin` no tiene autorización para ver, listar ni gestionar los productos ni las categorías de las tiendas (restringido exclusivamente a `store_owner` y `store_admin`). No aparecen en su navegación ni en sus permisos.

#### Credenciales de Acceso y Cuentas de Prueba

| Tipo de Cuenta | Rol | Correo / Usuario | Contraseña | Descripción / Alcance |
|---|---|---|---|---|
| **Super Administrador** | `super_admin` | `admin@example.com` | `password` | Acceso global a toda la plataforma en `/admin`. Administra ciudades, tiendas, planes y configuración global. |
| **Dueño de Tienda** | `store_owner` | `owner@example.com` | `password` | Asociado a todas las tiendas de prueba. Puede ingresar al panel `/admin`, ver su catálogo y alternar entre cualquier tienda mediante el botón *"Usar tienda"* en la tabla de empresas. |
| **Usuario Normal** | `user` | `user@example.com` | `password` | Usuario cliente registrado sin permisos de panel Filament. |

> El aislamiento por empresa se hace por membresía `company_user` + `TenantContext`; las policies Filament verifican permiso Shield (`Accion:Modelo`) **y** membresía de empresa. El listado de usuarios ya viene filtrado por el global scope `companyMembership` del modelo `User`.

> Las operaciones de sucursales de ambos roles están sujetas a la regla inicial de una sola sucursal por empresa. Esta actualización registra la decisión de negocio; la adaptación del código, los datos demo y las pruebas existentes queda pendiente. No eliminar sucursales existentes automáticamente.

## Diseño aprobado de la página principal

- El propietario aprobó `services/laravel/public/inicio-ejemplo.html` como base visual de la página principal.
- Conservar su dirección visual: violeta, fondos claros, tipografías DM Sans y Space Grotesk, fotografías y tarjetas de tiendas, con diseño responsivo.
- La página principal debe mostrar las tiendas y permitir abrir su catálogo al pulsarlas. Debe incluir un acceso visible a iniciar sesión en el panel existente para superadministrador y dueños de negocio.
- La maqueta incluye búsqueda y filtros; sus tiendas, fotografías y productos son ilustrativos y no representan datos de producción.
- Al pulsar una tienda se abrirá el catálogo de su única sucursal, usando la ruta existente con slug de tienda y UUID de sucursal. El cliente no tendrá que elegir ciudad ni sucursal.
- La decisión sobre el destino de las tarjetas cuando se habiliten varias sucursales queda para esa etapa futura. No introducir ahora selección automática entre varias sucursales.

## Rendimiento de Base de Datos y Optimización de Consultas (Regla Estricta)

- **Cero consultas N+1:** Toda tabla de Filament, controlador o presenter debe precargar las relaciones y agregaciones necesarias (`with()`, `withCount()`, `withSum()`, `counts()`, `sum()`). Queda prohibido ejecutar consultas SQL dentro de closures por fila (`getStateUsing`, `url`, `hidden`, `money`) sin que el dato venga resuelto en la consulta principal.
- **Índices compuestos alineados con las consultas reales:** Toda tabla o migración nueva debe definir índices compuestos que cubran las columnas de filtrado (`WHERE`, incluyendo `company_id` en tablas multi-tenant) y ordenamiento (`ORDER BY`), evitando *filesort* y *full table scans*.
- **Filtros de fecha amigables con índices:** Evitar funciones SQL sobre columnas indexadas como `whereDate('created_at', ...)` en tablas de alto volumen; usar rangos `where('created_at', '>=', ...)` para aprovechar los índices B-Tree.
- **Hooks de modelos (`booted` / `saving`) condicionales:** Nunca ejecutar consultas SQL en eventos `saving`/`creating` si la relación ya está cargada en memoria (`relationLoaded`) o si la llave foránea padre no fue modificada (`! $model->isDirty(...)`).
- **Agregaciones consolidadas:** En widgets y reportes, agrupar múltiples conteos o sumas de una misma tabla en una sola consulta SQL (`SELECT SUM(CASE WHEN ...)`).
- **Operaciones en lote y Seeders:** Envolver escrituras múltiples en `DB::transaction()` y priorizar `insertOrIgnore()` / `insert()` en lote frente a consultas repetitivas dentro de bucles.

## Comandos y verificación

Desde la raíz del repo (`/home/desarrollo/Documentos/PROYECTOS/Delivery/delivery`):

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
docker compose exec delivery-app php artisan catalog:ensure-defaults  # asegurar catálogo por defecto a todas las tiendas
```

> ⚠️ **NUNCA** ejecutes `php artisan migrate:fresh`, `db:wipe` o similares contra la base de datos de desarrollo sin permiso explícito (borran datos). Los tests usan SQLite en memoria.
> ⚠️ **NUNCA** ejecutes `php artisan shield:generate` sin `--ignore-existing-policies` (sobrescribe policies personalizadas).

Verificación rápida del catálogo público: abrir `http://localhost:8000/c/burger-demo/{uuid_sucursal}` (los enlaces se imprimen al correr `DemoStoreSeeder`) o usar la acción "Ver catálogo" en la tabla de sucursales del panel.

## Identidad de Marca y Branding (ChatPedido)

- **Nombre oficial de la marca / aplicación:** `ChatPedido` (dominio proyectado: `chatpedido.com`).
- **Concepto visual seleccionado:** Opción 2 ("El Ticket / Comanda Express" con rayo verde de confirmación rápida y punto de estado naranja).
- **Ubicación de recursos e identidad:** Se encuentra en la carpeta `branding` al mismo nivel que el repositorio `delivery`:
  - **Ruta absoluta:** `/home/desarrollo/Documentos/PROYECTOS/Delivery/branding/`
  - **Estructura:**
    - `manual-estilos-chatpedido.pdf`: Manual editorial completo en PDF (A4) con códigos de color, tipografía, retícula y reglas de tiendas.
    - `web/`: Logotipos horizontales SVG (versiones light y dark), isotipo SVG, isotipo PNG (512x512), `og-image-1200x630.png`, `apple-touch-icon.png`, `favicon.ico` (multi-capa 16/32/48), y favicons PNG.
    - `android/`: Icono oficial Google Play (`google-play-icon-512x512.png`), Gráfico de funciones promocional (`google-play-feature-graphic-1024x500.png`), icono adaptativo modular (`adaptive-icon-background.png` y `adaptive-icon-foreground.png` de 432x432), y mipmaps (`mdpi` a `xxxhdpi`).
    - `ios/`: Icono maestro App Store Connect (`app-store-icon-1024x1024.png` sin transparencia) y resoluciones completas para iPhone/iPad/Spotlight/Ajustes en Xcode.
- **Colores corporativos:**
  - Violeta Institucional: `#6135BC`
  - Violeta Claro / Acentos: `#A78BFA`
  - Verde WhatsApp / Confirmación: `#10B981`
  - Naranja Acción / Alerta: `#FF6B00`
  - Oscuro Profundo / Texto: `#1A162B`
- **Tipografías:** `Space Grotesk` (Títulos/Logotipo) y `DM Sans` (Cuerpo/UI).

## Estrategia de Dominios y Ruteo de Red (Web vs. API)

- **`chatpedido.com` (Dominio comercial principal):**
  - **Alcance:** Exclusivo para la aplicación web, catálogos públicos (`/c/{tienda}/{uuid}`), directorio de tiendas y panel administrativo Filament (`/admin`).
  - **Restricción estricta:** **NO debe aceptar peticiones de API**. Las rutas `/api/*` estarán bloqueadas bajo este dominio.
- **`tryironflow.com` (Dominio activo del propietario, vence en 2028):**
  - **Alcance:** Apuntará al mismo servidor / IP y será el endpoint oficial exclusivo para la API REST v1 (`/api/v1/...`).
  - **Uso:** Destinado a las aplicaciones móviles nativas de Android e iOS, integraciones y webhooks.
- **`vemitienda.com.ve` (Dominio anterior):**
  - **Estado:** Descartado definitivamente; no se utilizará.
- **Viabilidad y arquitectura:**
  - Es **100% posible y recomendado**. Se controla tanto a nivel de proxy inverso Nginx (bloqueando `location /api` en el server de ChatPedido) como a nivel de Laravel (`Route::domain()` o middleware de host).
