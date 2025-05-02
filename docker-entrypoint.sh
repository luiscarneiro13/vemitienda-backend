#!/bin/sh

# Esperar a MySQL
until mysqladmin ping -h"mysql" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" --silent; do
  echo "Esperando a MySQL..."
  sleep 2
done

# Ejecutar migraciones y seeds
php artisan migrate --force
php artisan db:seed --force

# Optimizar la aplicación
php artisan optimize:clear
php artisan optimize
php artisan storage:link

# Iniciar cron
service cron start

# Mantener el contenedor en ejecución
exec "$@"
