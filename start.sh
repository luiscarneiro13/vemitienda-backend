#!/bin/bash

echo "================================="
echo "Iniciando Proyecto Laravel con Docker 🚀"
echo "================================="
echo ""

# =========================
# Variables
# =========================
OPTION=2
START_QUEUE_WORK=yes
ENTER_CONTAINER=no
CONTAINERS=("vemitiendabackend-php" "vemitiendabackend-nginx")
APP_CONTAINER="vemitiendabackend-php"
NGINX_CONTAINER="vemitiendabackend-nginx"
EMAIL="carneiroluis2@gmail.com"

# =========================
# Detener y eliminar contenedores existentes
# =========================
stop_and_remove_container() {
    local container_name=$1
    if docker ps -a --filter "name=$container_name" --format "{{.Names}}" | grep -q "$container_name"; then
        echo ">> Deteniendo y eliminando el contenedor $container_name..."
        docker stop "$container_name" && docker rm "$container_name"
    else
        echo ">> El contenedor $container_name no existe o no está en ejecución."
    fi
}

echo ">> Deteniendo y eliminando contenedores existentes..."
stop_and_remove_container "$APP_CONTAINER"
stop_and_remove_container "$NGINX_CONTAINER"

echo ""
echo ">> Reconstruyendo contenedores y levantando servicios (docker compose up -d)..."
docker compose -f docker-compose.prod.yml up -d --build

# =========================
# Esperar a que los contenedores se levanten antes de ejecutar comandos
# =========================
wait_for_container() {
    local container_name=$1
    echo ">> Esperando a que el contenedor $container_name esté listo..."

    while ! docker ps --filter "name=$container_name" --format "{{.Names}}" | grep -q "$container_name"; do
        sleep 2
    done

    echo ">> Contenedor $container_name está en ejecución."
}

wait_for_container "$APP_CONTAINER"
wait_for_container "$NGINX_CONTAINER"

echo ""
echo ">> Instalando dependencias Composer y NPM..."
docker exec --tty=false "$APP_CONTAINER" chown -R www-data:www-data /var/www
docker exec --tty=false "$APP_CONTAINER" chmod -R 775 /var/www/storage/logs
docker exec --tty=false "$APP_CONTAINER" chmod -R 775 /var/www/storage /var/www/bootstrap/cache
docker exec --tty=false "$APP_CONTAINER" php artisan optimize:clear
docker exec --tty=false "$APP_CONTAINER" composer install --ignore-platform-req=ext-gd
docker exec --tty=false "$APP_CONTAINER" npm install

echo ""
echo ">> Ejecutando migraciones..."
docker exec --tty=false "$APP_CONTAINER" php artisan migrate

echo ""
echo ">> Iniciando queue:work..."
docker exec -d "$APP_CONTAINER" php artisan queue:work

echo "=========================================="
echo "¡Proyecto listo con Certbot instalado! 🔒🚀"
echo "=========================================="
