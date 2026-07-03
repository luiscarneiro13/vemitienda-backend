#!/bin/bash

echo "================================="
echo "Iniciando Proyecto Laravel con Docker 🚀"
echo "================================="
echo ""

# =========================
# Variables
# =========================
APP_CONTAINER="vemitiendabackend-php"
NGINX_CONTAINER="vemitiendabackend-nginx"
CHAT_CONTAINER="miwisachat-node"

echo ""
echo ">> Descargando imágenes externas (metube, certbot, nginx)..."
docker compose -f docker-compose.prod.yml pull

echo ""
echo ">> Reconstruyendo contenedores y levantando servicios..."
docker compose -f docker-compose.prod.yml up -d --build

# =========================
# Esperar a que los contenedores estén listos
# =========================
wait_for_container() {
    local container_name=$1
    local timeout=60
    local elapsed=0
    echo ">> Esperando a que el contenedor $container_name esté listo..."
    while ! docker ps --filter "name=$container_name" --format "{{.Names}}" | grep -q "$container_name"; do
        sleep 2
        elapsed=$((elapsed + 2))
        if [ $elapsed -ge $timeout ]; then
            echo "⚠️  Tiempo de espera agotado para $container_name. Continuando..."
            return 1
        fi
    done
    echo ">> Contenedor $container_name está en ejecución."
}

wait_for_container "$APP_CONTAINER"
wait_for_container "$NGINX_CONTAINER"
wait_for_container "$CHAT_CONTAINER"

echo ""
echo ">> Instalando dependencias Composer y NPM..."
docker exec "$APP_CONTAINER" chown -R www-data:www-data /var/www
docker exec "$APP_CONTAINER" chmod -R 775 /var/www/public/images /var/www/public/thumbnails /var/www/storage/logs /var/www/storage /var/www/bootstrap/cache
docker exec "$APP_CONTAINER" php artisan optimize:clear
docker exec "$APP_CONTAINER" composer install --ignore-platform-req=ext-gd
docker exec "$APP_CONTAINER" npm install

echo ""
echo ">> Ejecutando migraciones..."
docker exec "$APP_CONTAINER" php artisan migrate --force

echo ""
echo ">> Instalando passport..."
docker exec -d "$APP_CONTAINER" php artisan passport:install

echo ""
echo ">> queue:work corre como servicio 'queue-worker' administrado por docker-compose (restart: unless-stopped), no requiere inicio manual."

echo ""
echo ">> Creando enlace simbólico..."
docker exec -d "$APP_CONTAINER" php artisan storage:link

echo ""
echo ">> Limpiando imágenes huérfanas..."
docker image prune -af

echo "=========================================="
echo "¡Proyecto listo con Certbot instalado! 🔒🚀"
echo "=========================================="