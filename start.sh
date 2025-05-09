#!/bin/bash

echo "================================="
echo "Iniciando Proyecto Laravel con Docker 🚀"
echo "================================="
echo ""

# =========================
# Variables (Set defaults as if all "yes" were selected)
# =========================
OPTION=2
START_QUEUE_WORK=yes
ENTER_CONTAINER=no
CONTAINERS=("vemitienda-backend-php" "vemitienda-backend-nginx")
APP_CONTAINER="vemitienda-backend-php"

# =========================
# Acciones
# =========================

echo ">> Deteniendo contenedor vemitienda-backend-php..."

if docker ps --filter "name=vemitienda-backend-php" --format "{{.Names}}" | grep -q "vemitienda-backend-php"; then
    echo ">> Deteniendo el contenedor vemitienda-backend-php..."
    docker compose down react-native
else
    echo ">> El contenedor vemitienda-backend-php no está en ejecución."
fi

echo ">> Deteniendo contenedor vemitienda-backend-nginx..."

if docker ps --filter "name=vemitienda-backend-nginx" --format "{{.Names}}" | grep -q "vemitienda-backend-nginx"; then
    echo ">> Deteniendo el contenedor vemitienda-backend-nginx..."
    docker compose down react-native
else
    echo ">> El contenedor vemitienda-backend-nginx no está en ejecución."
fi

echo ""
echo ">> Reconstruyendo contenedores (docker compose build)..."
docker compose build

echo ""
echo ">> Levantando servicios (docker compose up -d)..."
docker compose up -d

echo ""
echo ">> Configurando 'host.docker.internal' dentro del contenedor..."
sleep 5

echo ""
echo ">> Instalando dependencias Composer y NPM..."
docker exec -it "$APP_CONTAINER" git config --global --add safe.directory /var/www
docker exec -it "$APP_CONTAINER" composer install
docker exec -it "$APP_CONTAINER" npm install

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

# if docker exec -it "$APP_CONTAINER" test -f /var/www/artisan; then
#     echo "Accede a tu proyecto Laravel en: http://localhost:9010"
# else
#     echo "Proyecto desplegado en contenedor '$APP_CONTAINER'"
# fi
