# Arquitectura de Despliegue Automático y Guía de Reemplazo de Laravel

Este documento describe cómo funciona el despliegue automático a producción en este repositorio y qué puntos deben ajustarse al reemplazar el proyecto Laravel actual (`vemitienda/`) por un nuevo sistema Laravel con su propia base de datos, manteniendo intacto `miwisachat/`.

---

## 1. Flujo de Despliegue Automático (CI/CD)

El despliegue se dispara automáticamente en cada `push` a la rama `main` mediante **GitHub Actions**:

1. **Trigger y conexión SSH** (`.github/workflows/deploy.yml`):
   - Usa `appleboy/ssh-action@v1.0.0` con los secrets `SSH_HOST`, `SSH_USER`, `SSH_PRIVATE_KEY` y `SSH_PORT` para conectarse al Droplet de DigitalOcean.
2. **Limpieza de contenedores**:
   - Detiene `vemitiendabackend-php`, `vemitiendabackend-nginx` y `miwisachat-node`.
   - Ejecuta `docker container prune -f` y verifica que no queden contenedores en estado `exited`.
3. **Actualización del código en el servidor**:
   - Entra a `/home/vemitiendabackend`, descarta cambios locales (`git checkout .`) y hace `git pull`.
   - Da permisos de ejecución a `start.sh`, `renovar_cert.sh` y `deploy-on-reboot.sh`.
4. **Orquestación y post-despliegue** (`start.sh`):
   - Descarga imágenes externas (`docker compose -f docker-compose.prod.yml pull`).
   - Reconstruye y levanta los servicios en segundo plano (`docker compose -f docker-compose.prod.yml up -d --build`).
   - Espera hasta 60s a que `vemitiendabackend-php`, `vemitiendabackend-nginx` y `miwisachat-node` estén en ejecución.
   - Dentro del contenedor PHP (`vemitiendabackend-php`), ejecuta:
     - Ajuste de permisos (`chown -R www-data:www-data /var/www`, `chmod -R 775 ...`).
     - `php artisan optimize:clear`
     - `composer install --ignore-platform-req=ext-gd`
     - `npm install`
     - `php artisan migrate --force`
     - `php artisan passport:install` (en segundo plano)
     - `php artisan storage:link` (en segundo plano)
   - Limpia imágenes huérfanas (`docker image prune -af`).

> **Nota sobre reinicios del servidor:** `deploy-on-reboot.sh` replica prácticamente los mismos pasos que `deploy.yml`, añadiendo al final `./renovar_cert.sh`.

---

## 2. Servicios en Producción (`docker-compose.prod.yml`)

| Servicio | Contenedor | Contexto / Imagen | Red / Puertos |
| :--- | :--- | :--- | :--- |
| `php` | `vemitiendabackend-php` | `./vemitienda/Dockerfile` | Red `vemitienda`, puerto `9000:80` |
| `queue-worker` | `vemitiendabackend-queue-worker` | Imagen `vemitienda-php` | Corre `php artisan queue:work --tries=3 --sleep=3` (`restart: unless-stopped`) |
| `nginx` | `vemitiendabackend-nginx` | `nginx:stable-alpine` | Puertos `80:80`, `443:443`. Monta `./vemitienda/nginx/default.prod.conf` |
| `miwisachat` | `miwisachat-node` | `./miwisachat/Dockerfile` | Expone `3977` internamente en red `vemitienda` |
| `certbot` | `vemitiendabackend-certbot` | `certbot/certbot` | Renueva SSL cada 12h vía webroot `/var/www/html` |

---

## 3. Puntos clave al reemplazar `vemitienda/` por el nuevo Laravel

Cuando coloques la carpeta del nuevo proyecto Laravel (ya sea reemplazando `vemitienda/` o usando otro nombre de carpeta):

1. **Rutas y volúmenes en Docker (`docker-compose.prod.yml` y `docker-compose.yml`)**:
   - Actualizar `dockerfile: ./<nueva-carpeta>/Dockerfile` y los volúmenes `./<nueva-carpeta>:/var/www` en `php`, `queue-worker` y `nginx`.
   - Si cambias el nombre de los contenedores (`vemitiendabackend-php`, `vemitiendabackend-nginx`), debes actualizar también:
     - `.github/workflows/deploy.yml` (en `docker stop ...`).
     - `start.sh` (`APP_CONTAINER`, `NGINX_CONTAINER`).
     - `deploy-on-reboot.sh` (en `docker stop ...`).
     - `fastcgi_pass` en la configuración de Nginx.

2. **Configuración de Nginx (`nginx/default.prod.conf`)**:
   - Mantener intactos los bloques de `miwisachat`:
     - `location /api/miwisachat/` -> `proxy_pass http://miwisachat-node:3977/api/miwisachat/;`
     - `location = /miwisachat` -> `return 301 /miwisachat/;`
     - `location /miwisachat/` -> `proxy_pass http://miwisachat-node:3977/miwisachat/;`
   - Si el nuevo Laravel usa otro dominio o subdominio, ajustar `server_name` y las rutas de certificados SSL (`/etc/letsencrypt/live/...`).

3. **Base de datos y `.env` del nuevo Laravel**:
   - En producción, el `.env` de Laravel vive en el servidor dentro de la carpeta montada (`/var/www/.env`, no versionado en Git).
   - Al crear la nueva base de datos en el servidor, configurar `DB_HOST`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` en el `.env` del nuevo proyecto.

4. **Comandos post-despliegue en `start.sh`**:
   - Revisar si el nuevo Laravel requiere `php artisan passport:install` o `npm install` en producción. Si no usa Passport (por ejemplo, si usa Sanctum o sesiones), conviene quitar `passport:install` de `start.sh`.
   - Verificar que las rutas de `chmod` en `start.sh` (`public/images`, `public/thumbnails`, etc.) existan en el nuevo proyecto o ajustarlas para evitar errores en el script.
   - Si el nuevo proyecto no usa colas en base de datos, se puede desactivar o ajustar el servicio `queue-worker` en `docker-compose.prod.yml`.
