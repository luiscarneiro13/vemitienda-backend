# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Proyecto

**Ve mi Tienda** es una plataforma SaaS de e-commerce multi-tenant en Laravel 8. Cada usuario tiene su propia tienda (Company) con catálogo de productos, carrito de compras y órdenes. Incluye descarga asíncrona de videos YouTube, broadcasting en tiempo real con Ably, y almacenamiento en DigitalOcean Spaces.

El código Laravel vive en `vemitienda/` — la raíz del repositorio solo contiene Docker y scripts de despliegue.

## Comandos principales

Todos los comandos `php artisan` y `composer` se ejecutan dentro del contenedor o desde `vemitienda/`:

```bash
# Levantar entorno local (no autoarrancan; iniciar manualmente)
docker compose up -d --build

# Migraciones
docker compose exec php php artisan migrate
docker compose exec php php artisan migrate --seed

# Cola de trabajo (procesa jobs de email, descarga de videos, etc.)
docker compose exec php php artisan queue:work

# Compilar assets (desde vemitienda/)
npm run dev       # desarrollo
npm run watch     # watch mode
npm run production

# Tests (SQLite :memory: en tests)
./vendor/bin/phpunit
./vendor/bin/phpunit tests/Unit
./vendor/bin/phpunit tests/Feature

# Comandos de instalación inicial
docker compose exec php php artisan passport:install
docker compose exec php php artisan storage:link
```

**Puertos dev:** Nginx en `8000`, PHP-FPM en `9000`.

## Arquitectura

### Versionado de API

Coexisten dos versiones en `routes/api.php`:
- `V1` (`app/Http/Controllers/API/V1/`) — legacy, solo login/register
- `V3` (`app/Http/Controllers/API/V3/`) — versión activa con 15+ controladores

Las rutas V3 autenticadas usan `auth:api` (Laravel Passport).

### Repository Pattern

`app/Repositories/` encapsula todo el acceso a datos. Los controladores NO consultan Eloquent directamente — delegan en repositorios. Los repositorios usan métodos estáticos y acceden a `request()` global y `Auth::user()`. Hay 8 repositorios: Users, Products, Categories, Companies, Orders, Posts, Plans, Themes.

### Strategy Pattern para emails

`app/Strategies/SendEmail/` define estrategias por tipo de email (Contacto, ActivarCuenta, RecuperarCuenta, OrdenCompra, EmailSoporte). El Job `SendEmailJob` resuelve la estrategia correcta en runtime. Para agregar un tipo de email nuevo, crear la estrategia e incluirla en el array `STRATEGY` del job.

### Imágenes polimórficas

El modelo `Image` sirve a múltiples entidades (User, Product, Company, Post) via relación polimórfica `morphMany`/`morphOne`. Todas las imágenes se almacenan en **DigitalOcean Spaces** (disco `do`, driver S3) — no en almacenamiento local. Ver `app/Helpers/Images.php`.

### Jobs asíncronos (queue: database)

`app/Jobs/` contiene jobs para:
- Descarga de audio/video YouTube con `yt-dlp` via `Symfony\Component\Process`
- Envío de emails (`SendEmailJob`)

Los jobs de descarga emiten eventos broadcast (`InicioDescarga`, `DescargaExitosa`, `DescargaFallida`) por el canal `canal-chat` usando Ably.

### Trait ApiResponser

`app/Traits/ApiResponser.php` — todos los controladores API lo usan para respuestas JSON estandarizadas. Usar `successResponse()`, `errorResponse()`, `errorValidation()`, `unauthorizedResponse()`, `handleExceptions()`.

## Variables de entorno críticas

```bash
BROADCAST_DRIVER=ably          # WebSockets real-time
ABLY_KEY=...                   # API key de Ably

DO_ACCESS_KEY_ID=...           # DigitalOcean Spaces (imágenes)
DO_SECRET_ACCESS_KEY=...
DO_BUCKET=vemitienda
DO_ENDPOINT=https://nyc3.digitaloceanspaces.com

QUEUE_CONNECTION=database      # Jobs en tabla `jobs`

# OAuth Google
GOOGLE_CLIENT_ID=...
GOOGLE_CLIENT_SECRET=...

# Passport
# Requiere: php artisan passport:install (genera claves en storage/)
```

## Despliegue

**Producción:** GitHub Actions (push a `main`) hace SSH al Droplet de DigitalOcean y ejecuta `./start.sh`. El script reconstruye imágenes Docker, migra, reinstala Passport y arranca workers.

**Worker de colas:** el servicio `queue-worker` de `docker-compose.prod.yml` (y `docker-compose.yml` en dev) corre `php artisan queue:work --tries=3 --sleep=3` con `restart: unless-stopped` — Docker lo reinicia solo si el proceso muere. (`script-correr-colas.txt` es un script standalone para instalar Supervisor a nivel de host; no se ejecuta desde `start.sh` ni desde el workflow de deploy — es un remanente de un setup previo a la dockerización).

**docker-compose.prod.yml** incluye servicios adicionales: Certbot (SSL) y Metube (descarga de videos vía yt-dlp).

## Carrito de compras

Implementado con `darryldecode/cart` basado en sesión. Controlador: `app/Http/Controllers/WEB/V3/CartController.php`. Las rutas son web (no API) en `routes/web.php`.

## Multi-tenancy

Cada `User` tiene un `Company` con slug único. Las URLs públicas del catálogo son `/{slug}` y `/catalogo/{slug}` (legacy). El `Company` calcula `url_tienda` y `url_catalogo` como atributos computados.

## MiWisaChat Web

El backend Node de `miwisachat/` (independiente de Laravel, ver su propio `app.js`) sirve también la versión web de la app de chat bajo la ruta `/miwisachat` — distinta del prefijo de API `/api/miwisachat`. El build estático se genera en el repo separado del cliente (`MiWisaChat/cliente`) con `npx expo export -p web` y se copia manualmente a `miwisachat/public-web/` antes de desplegar. No hay CI para el repo del cliente: es un paso manual, similar a como `npm run production` compila los assets de Laravel en este repo. Nginx (`vemitienda/nginx/default.prod.conf`) hace proxy de `/miwisachat/` al contenedor `miwisachat-node`, igual que ya hace con `/api/miwisachat/`.
