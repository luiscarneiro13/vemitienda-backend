# Usa la imagen oficial de PHP-FPM con Alpine Linux
FROM php:8.2-fpm-alpine

# Instala dependencias necesarias
RUN apk add --no-cache bash mysql-client zip unzip

# Instala extensiones de PHP recomendadas para Laravel
RUN docker-php-ext-install pdo pdo_mysql bcmath intl zip

# Instala Composer desde la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Define el directorio de trabajo
WORKDIR /var/www

# Configura permisos correctos para Laravel y Nginx
RUN chown -R www-data:www-data /var/www && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

