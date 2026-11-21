# Instalación

## Requisitos

- Docker Engine
- Docker Compose v2 (`docker compose ...`, sin guion)

No se requiere PHP, Composer ni Node instalados en el host: todo corre dentro de los contenedores.

## Pasos

### 1. Clonar y configurar variables de entorno

```bash
git clone <url-del-repo> mi-proyecto
cd mi-proyecto

cp .env.example .env
cp services/laravel/.env.example services/laravel/.env
```

Hay **dos** archivos `.env` con responsabilidades distintas:

- `.env` (raíz): variables que Docker Compose necesita para interpolar `docker-compose.yml` (credenciales de MySQL, puerto expuesto).
- `services/laravel/.env`: el `.env` real de Laravel (`APP_KEY`, `DB_*`, `MAIL_*`, etc.), el único que Artisan/PHP leen en runtime.

Ver [docker.md](docker.md) para la relación exacta entre ambos.

### 2. Levantar los servicios

```bash
docker compose up -d
```

Esto construye la imagen de la aplicación (PHP 8.3-FPM + extensiones + Composer + Node) y levanta:

- `delivery-app` — PHP-FPM
- `delivery-worker` — `queue:work`
- `delivery-nginx` — servidor web en `http://localhost:8000`
- `delivery-mysql` — MySQL 8

La primera vez que arranca `delivery-app`, su `entrypoint.sh` detecta que no existe `vendor/` y ejecuta `composer install` automáticamente.

### 3. Generar la clave de la aplicación (si no existe)

```bash
docker compose exec delivery-app php artisan key:generate
```

Si cambias `APP_KEY` con el contenedor ya corriendo, reinícialo para que relea el `.env`:

```bash
docker compose up -d --force-recreate delivery-app delivery-worker
docker compose restart delivery-nginx
```

(nginx cachea la resolución DNS del contenedor `app`; tras recrearlo, reinicia nginx también).

### 4. Migrar y sembrar datos iniciales

```bash
docker compose exec delivery-app php artisan migrate --seed
```

Esto crea las tablas, genera los permisos de Filament Shield, los permisos de negocio, los roles (`super_admin`, `admin`, `manager`, `user`), una empresa demo, el usuario administrador y configuración inicial.

**Credenciales del administrador**: `admin@example.com` / `password`.

### 5. Compilar assets del frontend

```bash
docker compose exec delivery-app npm install
docker compose exec delivery-app npm run build
```

Esto no se automatiza en el arranque del contenedor para no requerir Node en el worker ni ralentizar el arranque. Durante desarrollo activo del frontend, puedes usar en su lugar:

```bash
docker compose exec delivery-app npm run dev
```

### 6. Verificar

- `http://localhost:8000` — landing / login de Breeze
- `http://localhost:8000/admin` — panel de Filament

## Reinstalar desde cero

```bash
docker compose down -v   # elimina también los volúmenes (BD, uploads)
docker compose up -d
docker compose exec delivery-app php artisan migrate:fresh --seed
```
