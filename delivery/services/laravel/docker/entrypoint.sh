#!/bin/sh
set -e

cd /var/www

if [ ! -f vendor/autoload.php ]; then
    # El lock debe vivir en el bind mount compartido (/var/www), no en /tmp:
    # app y worker tienen /tmp independientes, así que un flock en /tmp no
    # serializa nada entre contenedores. storage/framework sí es compartido.
    mkdir -p storage/framework
    (
        flock -x 200
        if [ ! -f vendor/autoload.php ]; then
            echo "[entrypoint] Instalando dependencias de Composer..."
            composer install --no-interaction --prefer-dist
        fi
    ) 200>/var/www/storage/framework/composer-install.lock
fi

mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache
chmod -R ugo+rwX storage bootstrap/cache || true

if [ -f /var/www/docker/zz-fpm-tuning.conf ] && [ -d /usr/local/etc/php-fpm.d ]; then
    cp /var/www/docker/zz-fpm-tuning.conf /usr/local/etc/php-fpm.d/zz-fpm-tuning.conf || true
fi

exec "$@"
