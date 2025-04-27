#!/bin/bash

set -e

# =========================
# Verificar si 'whiptail' está instalado
# =========================
if ! command -v whiptail &> /dev/null
then
    echo ""
    echo "======================================"
    echo "🚨 ERROR: El programa 'whiptail' no está instalado."
    echo "Debes instalarlo primero para poder usar el menú interactivo."
    echo ""
    echo "Puedes instalarlo ejecutando:"
    echo ""
    echo "    sudo apt-get update && sudo apt-get install whiptail"
    echo ""
    echo "======================================"
    exit 1
fi

# =========================
# Menú Interactivo
# =========================
echo "================================="
echo "Configuración inicial 🚀"
echo ""

# Obtener dimensiones de pantalla
HEIGHT=$(tput lines)
WIDTH=$(tput cols)

OPTION=$(whiptail --title "Luis Carneiro - Instalador de Proyecto" --radiolist \
"Selecciona una opción:" $((HEIGHT-10)) $((WIDTH-10)) 4 \
"1" "Construir contenedor, instalar Composer y NPM" ON \
"2" "Construir contenedor, instalar Composer, NPM y correr migraciones" OFF 3>&1 1>&2 2>&3)

# Si el usuario cancela
if [ $? -ne 0 ]; then
    echo "🚪 Saliste del instalador. Cancelando..."
    exit 1
fi

# Detectar y agregar host.docker.internal dinámicamente
echo ">> Configurando 'host.docker.internal' en /etc/hosts..."
WINDOWS_IP=$(cat /etc/resolv.conf | grep nameserver | awk '{print $2}')
if grep -q "host.docker.internal" /etc/hosts; then
    echo "✔ 'host.docker.internal' ya está configurado."
else
    echo "${WINDOWS_IP} host.docker.internal" | sudo tee -a /etc/hosts > /dev/null
    echo "✔ 'host.docker.internal' agregado correctamente."
fi

# =========================
# Variables
# =========================
SERVICE_NAME=app
CONTAINER_NAME=ventiendabackend-app

# =========================
# Acciones
# =========================
echo ""
echo "================================="
echo "Configuración completa. Iniciando... 🚀"
echo ""

# 1. Detener contenedor si existe
if [ "$(docker ps -a -q -f name=^${CONTAINER_NAME}$)" ]; then
    echo ">> Deteniendo y eliminando contenedor ${CONTAINER_NAME} existente..."
    docker stop ${CONTAINER_NAME}
    docker rm ${CONTAINER_NAME}
fi

# 2. Reconstruir contenedor
echo ">> Reconstruyendo el contenedor ${CONTAINER_NAME}..."
docker compose build

# 3. Levantar servicio
echo ">> Iniciando servicio ${SERVICE_NAME}..."
docker compose up -d ${SERVICE_NAME}

# 4. Configurar 'host.docker.internal' dinámicamente dentro del contenedor
echo ">> Configurando 'host.docker.internal' dentro del contenedor..."
WINDOWS_IP=$(cat /etc/resolv.conf | grep nameserver | awk '{print $2}')
docker exec -it ${CONTAINER_NAME} bash -c "echo '${WINDOWS_IP} host.docker.internal' >> /etc/hosts"

# 5. Esperar unos segundos
sleep 5

# 6. Acciones según la opción
echo ""
case $OPTION in
    "1")
        echo ">> Opción 1 seleccionada: Solo instalar Composer y NPM..."
        docker exec -it ${CONTAINER_NAME} git config --global --add safe.directory /var/www
        docker exec -it ${CONTAINER_NAME} composer install
        docker exec -it ${CONTAINER_NAME} npm install
        ;;
    "2")
        echo ">> Opción 2 seleccionada: Instalar Composer, NPM y correr migraciones..."
        docker exec -it ${CONTAINER_NAME} git config --global --add safe.directory /var/www
        docker exec -it ${CONTAINER_NAME} composer install
        docker exec -it ${CONTAINER_NAME} npm install
        docker exec -it ${CONTAINER_NAME} php artisan migrate
        ;;
esac

# 7. Preguntar si iniciar queue:work
if (whiptail --title "Colas" --yesno "¿Deseas iniciar 'php artisan queue:work' en segundo plano?" 10 60); then
    echo ">> Iniciando queue:work en segundo plano..."
    docker exec -d ${CONTAINER_NAME} php artisan queue:work
fi

# 8. Preguntar si entrar al contenedor
if (whiptail --title "Entrar al contenedor" --yesno "¿Deseas entrar al contenedor ahora?" 10 60); then
    echo ">> Entrando al contenedor ${CONTAINER_NAME}..."
    docker exec -it ${CONTAINER_NAME} bash
else
    echo ">> Listo 🚀 Contenedor ${CONTAINER_NAME} corriendo."
fi

# =========================
# Pantalla Final Bonita
# =========================
echo ""
echo "==========================================="
echo "🎉 ¡Proyecto listo para trabajar!"
echo "👨‍💻 Desarrollado por Luis Carneiro"
echo ""

# Detectar si es Laravel
if docker exec -it ${CONTAINER_NAME} test -f /var/www/artisan; then
    echo "🔗 Accede a tu proyecto Laravel en: http://localhost:8000"
else
    echo "🔗 Proyecto desplegado en contenedor '${CONTAINER_NAME}'"
fi

echo "==========================================="