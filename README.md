# Esta configuración incluye:

- Optimización de imágenes Docker

- Configuración de seguridad para Nginx

- Redis para cache y colas

- Cron para tareas programadas

- Supervisión de procesos

- Manejo adecuado de permisos

- Variables de entorno separadas

- Sistema de caché para builds rápidos

- Configuraciones de performance (OPcache, PHP-FPM, Nginx)


## Para usar el sistema:

    Configuración inicial:

        chmod +x docker-entrypoint.sh
        cp .env.docker .env

## Construir y ejecutar:

    docker-compose up -d --build
    
## Flujo completo automatizado:

- Construye la imagen PHP con todas las dependencias

- Configura MySQL con persistencia de datos

- Instala dependencias de Composer y NPM

- Compila assets frontend

- Ejecuta migraciones y seeds

- Inicia PHP-FPM, Nginx, Queue Worker y Scheduler

- Configura cron para tareas programadas

- Habilita Redis para cache y colas

- Configura opcache para mejor performance

- Comandos útiles adicionales:


## Ejecutar tests
    docker-compose exec app php artisan test

## Instalar nueva dependencia PHP
    docker-compose exec app composer require vendor/package

## Instalar nueva dependencia NPM
    docker-compose exec app npm install package-name

## Ver logs de la aplicación
    docker-compose logs -f app

## Acceder a MySQL
    docker-compose exec mysql mysql -u root -p
