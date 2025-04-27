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
# Función de barra de progreso
# =========================
function progress_bar() {
    {
    for ((i = 0 ; i <= 100 ; i+=20)); do
        echo $i
        sleep 0.2
    done
    } | whiptail --gauge "$1" 6 60 0
}

# =========================
# Menú Interactivo (Preguntar TODO al inicio)
# =========================
HEIGHT=$(tput lines)
WIDTH=$(tput cols)

OPTION=$(whiptail --title "Luis Carneiro - Instalador de Proyecto" --radiolist \
"Selecciona una opción:" $((HEIGHT-10)) $((WIDTH-10)) 4 \
"1" "Construir contenedor, instalar Composer y NPM" ON \
"2" "Construir contenedor, instalar Composer, NPM y correr migraciones" OFF 3>&1 1>&2 2>&3)

if [ $? -ne 0 ]; then
    echo "🚪 Saliste del instalador. Cancelando..."
    exit 1
fi

START_QUEUE_WORK=$(whiptail --title "Colas" --yesno "¿Deseas iniciar 'php artisan queue:work' en segundo plano?" 10 60 3>&1 1>&2 2>&3 && echo "yes" || echo "no")
ENTER_CONTAINER=$(whiptail --title "Entrar al contenedor" --yesno "¿Deseas entrar al contenedor ahora?" 10 60 3>&1 1>&2 2>&3 && echo "yes" || echo "no")

# =========================
# Variables
# =========================
CONTAINERS=("vemitiendabackend-app" "vemitiendabackend-nginx")
APP_CONTAINER="vemitiendabackend-app"

echo ""
echo "================================="
echo "Configuración completa. Iniciando... 🚀"
echo ""

# =========================
# Acciones
# =========================

progress_bar "Deteniendo contenedores existentes..."
for CONTAINER_NAME in "${CONTAINERS[@]}"; do
    if [ "$(docker ps -a -q -f name=^${CONTAINER_NAME}$)" ]; then
        docker stop ${CONTAINER_NAME}
        docker rm ${CONTAINER_NAME}
    fi
done

progress_bar "Reconstruyendo contenedores (docker compose build)..."
docker compose build

progress_bar "Levantando servicios (docker compose up -d)..."
docker compose up -d

 progress_bar "Configurando 'host.docker.internal' dentro del contenedor..."
# WINDOWS_IP=$(cat /etc/resolv.conf | grep nameserver | awk '{print $2}')
# docker exec -it ${APP_CONTAINER} bash -c "echo '${WINDOWS_IP} host.docker.internal' >> /etc/hosts"

sleep 5

progress_bar "Instalando dependencias Composer y NPM..."
docker exec -it ${APP_CONTAINER} git config --global --add safe.directory /var/www
docker exec -it ${APP_CONTAINER} composer install
docker exec -it ${APP_CONTAINER} npm install

if [ "$OPTION" == "2" ]; then
    progress_bar "Ejecutando migraciones..."
    docker exec -it ${APP_CONTAINER} php artisan migrate
fi

if [ "$START_QUEUE_WORK" == "yes" ]; then
    progress_bar "Iniciando queue:work..."
    docker exec -d ${APP_CONTAINER} php artisan queue:work
fi

if [ "$ENTER_CONTAINER" == "yes" ]; then
    echo ">> Entrando al contenedor ${APP_CONTAINER}..."
    docker exec -it ${APP_CONTAINER} bash
else
    echo ">> Listo 🚀 Contenedor ${APP_CONTAINER} corriendo."
fi

# Mensaje final
echo ""
echo "==========================================="
echo "🎉 ¡Proyecto listo para trabajar!"
echo "👨‍💻 Desarrollado por Luis Carneiro"
echo ""

if docker exec -it ${APP_CONTAINER} test -f /var/www/artisan; then
    echo "🔗 Accede a tu proyecto Laravel en: http://localhost:9010"
else
    echo "🔗 Proyecto desplegado en contenedor '${APP_CONTAINER}'"
fi

echo "==========================================="
