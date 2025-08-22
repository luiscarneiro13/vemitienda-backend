@echo off
setlocal enabledelayedexpansion

REM =========================
REM Variables (Set defaults as if all "yes" were selected)
REM =========================
set OPTION=2
set START_QUEUE_WORK=yes
set ENTER_CONTAINER=no
set CONTAINERS="vemitiendabackend-app" "vemitiendabackend-nginx"
set APP_CONTAINER=vemitiendabackend-app

echo =================================
echo Iniciando Proyecto Laravel con Docker
echo =================================
echo.


REM =========================
REM Acciones
REM =========================

echo Deteniendo contenedor vemitiendabackend-app...

docker ps --filter "name=vemitiendabackend-app" --format "{{.Names}}" | findstr /C:"vemitiendabackend-app" >nul 2>&1
if %errorlevel% == 0 (
    echo >> Deteniendo el contenedor vemitiendabackend-app...
    docker compose down react-native
) else (
    echo >> El contenedor vemitiendabackend-app no está en ejecución.
)

echo Deteniendo contenedor vemitiendabackend-nginx...

docker ps --filter "name=vemitiendabackend-nginx" --format "{{.Names}}" | findstr /C:"vemitiendabackend-nginx" >nul 2>&1
if %errorlevel% == 0 (
    echo >> Deteniendo el contenedor vemitiendabackend-nginx...
    docker compose down react-native
) else (
    echo >> El contenedor vemitiendabackend-nginx no está en ejecución.
)

echo.
echo Reconstruyendo contenedores (docker compose build)...
docker compose build

echo.
echo Levantando servicios (docker compose up -d)...
docker compose up -d

echo.
echo Configurando 'host.docker.internal' dentro del contenedor...
REM No se puede hacer esto directamente en Windows, depende de la configuracion de red en WSL
REM Intentar con host.docker.internal en la variable de entorno de docker-compose.yml
timeout /t 5 /nobreak >nul

echo.
echo Instalando dependencias Composer y NPM...
docker exec -it %APP_CONTAINER% git config --global --add safe.directory /var/www
docker exec -it %APP_CONTAINER% composer install
docker exec -it %APP_CONTAINER% npm install

echo.
echo Ejecutando migraciones...
docker exec -it %APP_CONTAINER% php artisan migrate

echo.
echo Iniciando queue:work...
docker exec -d %APP_CONTAINER% php artisan queue:work

echo.
echo Listo. Contenedor %APP_CONTAINER% corriendo.

echo.
echo ===========================================
echo Proyecto listo para trabajar!
echo Desarrollado por Luis Carneiro
echo.

docker exec -it %APP_CONTAINER% test -f /var/www/artisan
if %errorlevel% equ 0 (
    echo Accede a tu proyecto Laravel en: http://localhost:9010
) else (
    echo Proyecto desplegado en contenedor '%APP_CONTAINER%'
)

echo ===========================================

pause
