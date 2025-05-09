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

# =========================
# Función para detener y eliminar contenedores existentes
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

# =========================
# Acciones
# =========================

echo ">> Deteniendo y eliminando contenedores existentes..."
stop_and_remove_container "vemitiendabackend-php"
stop_and_remove_container "vemitiendabackend-nginx"


# =========================
# Función para esperar a que el contenedor esté listo
# =========================
wait_for_container() {
    local container_name=$1
    echo ">> Esperando a que el contenedor $container_name esté listo..."

    while ! docker ps --filter "name=$container_name" --format "{{.Names}}" | grep -q "$container_name"; do
        sleep 2
    done

    echo ">> Contenedor $container_name está en ejecución."
}


echo ""
echo ">> Reconstruyendo contenedores (docker compose build)..."
docker compose build

echo ""
echo ">> Levantando servicios (docker compose up -d)..."
docker compose up -d

# Esperar a que los contenedores se levanten antes de ejecutar comandos sobre ellos
wait_for_container "$APP_CONTAINER"

echo ""
echo ">> Configurando 'host.docker.internal' dentro del contenedor..."
sleep 5

echo ""
echo ">> Instalando dependencias Composer y NPM..."

docker exec -it "$APP_CONTAINER" git config --global --add safe.directory /var/www
docker exec -it "$APP_CONTAINER" composer install --ignore-platform-req=ext-gd
docker exec -it "$APP_CONTAINER" npm install

echo ""
echo ">> Copiando .env"
docker exec -it "$APP_CONTAINER" cp .env.docker .env

echo ""
echo ">> Ejecutando migraciones..."
docker exec -it "$APP_CONTAINER" php artisan migrate

echo ""
echo ">> Iniciando queue:work..."
docker exec -d "$APP_CONTAINER" php artisan queue:work

echo ""
echo ">> Listo. Contenedor $APP_CONTAINER corriendo."
echo "=========================================="
echo "Proyecto listo para trabajar!"
echo "Desarrollado por Luis Carneiro"
echo "=========================================="
