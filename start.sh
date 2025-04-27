#!/bin/bash

echo "================================="
echo "Configuración inicial 🚀"
echo ""

# Preguntamos todo de una vez
echo "¿Deseas correr 'php artisan migrate' después de levantar? (s/n)"
read MIGRATE

echo "¿Deseas iniciar 'php artisan queue:work' en segundo plano? (s/n)"
read QUEUE

echo "¿Deseas entrar al contenedor al finalizar todo? (s/n)"
read ENTER

echo ""
echo "================================="
echo "Configuración completa. Iniciando... 🚀"
echo ""

# Nombre del contenedor
CONTAINER_NAME=ventiendabackend-app

# --- Ejecuciones ---

# 1. Detener y eliminar contenedor anterior si existe
if [ "$(docker ps -a -q -f name=^${CONTAINER_NAME}$)" ]; then
  echo ">> Deteniendo y eliminando contenedor ${CONTAINER_NAME} existente..."
  docker stop ${CONTAINER_NAME}
  docker rm ${CONTAINER_NAME}
fi

# 2. Reconstruir contenedor
echo ">> Reconstruyendo el contenedor ${CONTAINER_NAME}..."
docker compose build

# 3. Levantar contenedor
echo ">> Iniciando servicio ${CONTAINER_NAME}..."
docker compose up -d ${CONTAINER_NAME}

# 4. Esperamos unos segundos
sleep 5

# 5. Correr migraciones si eligió "s"
if [[ "$MIGRATE" == "s" || "$MIGRATE" == "S" ]]; then
  echo ">> Ejecutando migraciones..."
  docker exec -it ${CONTAINER_NAME} php artisan migrate
fi

# 6. Iniciar queue:work en segundo plano si eligió "s"
if [[ "$QUEUE" == "s" || "$QUEUE" == "S" ]]; then
  echo ">> Iniciando queue:work en segundo plano..."
  docker exec -d ${CONTAINER_NAME} php artisan queue:work
fi

# 7. Entrar al contenedor si eligió "s"
if [[ "$ENTER" == "s" || "$ENTER" == "S" ]]; then
  echo ">> Entrando al contenedor ${CONTAINER_NAME}..."
  docker exec -it ${CONTAINER_NAME} bash
else
  echo ">> Listo 🚀 Contenedor ${CONTAINER_NAME} corriendo."
fi
