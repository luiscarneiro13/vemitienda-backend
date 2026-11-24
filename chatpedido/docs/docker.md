# Docker

## Servicios (desarrollo — `docker-compose.yml`)

| Servicio | Container | Imagen/Build | Descripción |
|---|---|---|---|
| `delivery-app` | `delivery-app` | `services/laravel/Dockerfile` (stage `dev`) | PHP-FPM 8.3 |
| `delivery-worker` | `delivery-worker` | igual que `app` | `php artisan queue:work --tries=3 --sleep=3` |
| `delivery-nginx` | `delivery-nginx` | `nginx:stable-alpine` | expone `8000:80`, usa `nginx/default.conf` |
| `delivery-mysql` | `delivery-mysql` | `mysql:8.0` | base `delivery`, volumen `mysql_data`, con healthcheck |

Todos los servicios se comunican mediante la red `delivery-network`. Laravel se conecta a MySQL usando `DB_HOST=delivery-mysql` (el nombre del *servicio*, resuelto vía DNS interno de Docker Compose).

## Los dos `.env`

| Archivo | Quién lo lee | Contenido |
|---|---|---|
| `.env` (raíz) | Docker Compose, al interpolar `docker-compose.yml` | `MYSQL_ROOT_PASSWORD`, `MYSQL_DATABASE`, `MYSQL_USER`, `MYSQL_PASSWORD`, `APP_PORT` |
| `services/laravel/.env` | Laravel/Artisan en runtime, vía `env_file` en el compose | `APP_KEY`, `APP_URL`, `DB_*`, `MAIL_*`, `QUEUE_*`, etc. |

**Regla de consistencia**: `DB_DATABASE`/`DB_USERNAME`/`DB_PASSWORD` en `services/laravel/.env` deben coincidir exactamente con `MYSQL_DATABASE`/`MYSQL_USER`/`MYSQL_PASSWORD` en el `.env` raíz — son las mismas credenciales vistas desde dos lados distintos (el contenedor de MySQL las usa para crear el usuario/base; Laravel las usa para conectarse).

Ambos `.env.example` se versionan en git; los `.env` reales están en `.gitignore`.

## Dockerfile multi-stage (`services/laravel/Dockerfile`)

- **`base`**: PHP 8.3-FPM Alpine + Composer + extensiones (`pdo_mysql`, `mbstring`, `bcmath`, `gd`, `zip`, `exif`, `pcntl`, `intl`, `opcache`).
- **`dev`**: añade Node/npm y usa `docker/entrypoint.sh` como `ENTRYPOINT`. En cada arranque, si no existe `vendor/autoload.php`, ejecuta `composer install` (protegido con `flock` para evitar condiciones de carrera entre `app` y `worker`, que montan el mismo código). También ajusta permisos de `storage/` y `bootstrap/cache/`.
- **`node_builder`**: `node:20-alpine`, compila los assets de Vite (`npm run build`).
- **`prod`**: copia el código completo (sin bind mount), corre `composer install --no-dev --optimize-autoloader`, copia los assets ya compilados desde `node_builder`, y usa `docker/entrypoint.prod.sh` para cachear config/rutas/vistas **en el arranque del contenedor** (no en build time — las variables de entorno reales solo existen en runtime, vía `env_file`).

### ¿Por qué `composer install` no va en el build de dev?

Porque `services/laravel` está montado como bind mount (`./services/laravel:/var/www`): cualquier `vendor/` generado en build time quedaría oculto por el contenido del host en cuanto el volumen se monte. Por eso en dev se instala en el `entrypoint.sh`, y en producción (sin bind mount) sí se instala en build time.

## Producción (`docker-compose.prod.yml`)

- Usa el stage `prod` del mismo Dockerfile (`build.target: prod`).
- Sin bind mount de código fuente — la imagen es inmutable.
- `public/` se comparte entre `app` y `nginx` vía el volumen nombrado `laravel_public` (Docker copia automáticamente el contenido ya compilado de la imagen al volumen la primera vez que se monta).
- `laravel_storage` persiste `storage/app` (uploads) entre despliegues.
- No se expone el puerto de MySQL al host.
- Opcache se configura con `validate_timestamps=0` para máximo rendimiento (los cambios de código requieren reconstruir la imagen).

```bash
docker compose -f docker-compose.prod.yml up -d --build
docker compose -f docker-compose.prod.yml exec delivery-app php artisan migrate --force
```

En producción, ajusta en `services/laravel/.env`: `APP_ENV=production`, `APP_DEBUG=false`, y considera `LOG_CHANNEL=daily` para que la página **Logs** del panel Filament siga siendo útil.

## Problemas comunes

- **Puerto ocupado**: si `3306` u otro puerto ya está en uso por otro servicio del host (p. ej. otro proyecto Docker), ajusta `APP_PORT` en `.env`, o no publiques el puerto de MySQL si no lo necesitas expuesto (por defecto esta plantilla no lo expone).
- **`nginx` responde 502 tras recrear `app`**: nginx resuelve la IP del contenedor `app` una sola vez y la cachea; si recreaste `delivery-app` (nueva IP), reinicia `delivery-nginx`.
- **Permisos de archivos generados por `artisan make:*`**: el proceso dentro del contenedor corre como `root`, así que los archivos nuevos quedan con ese propietario en el host. Ejecuta `sudo chown -R $(id -u):$(id -g) services/laravel` si necesitas editarlos desde el host.
