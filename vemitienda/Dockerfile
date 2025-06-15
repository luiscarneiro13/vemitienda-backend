# Etapa 1: construcción e instalación de dependencias
FROM php:8.2-fpm-alpine AS builder

# Instala dependencias necesarias para compilación e instalación de extensiones
RUN apk add --no-cache \
    bash git zip unzip mysql-client \
    freetype-dev libjpeg-turbo-dev libpng-dev libwebp-dev \
    icu-dev libzip-dev curl

# Instala extensiones recomendadas para Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install pdo pdo_mysql bcmath intl zip gd

# Instala Composer
COPY --from=composer:2.5 /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

# Copia archivos de Laravel (excepto node_modules, tests, etc.)
COPY ./vemitienda .

# Instala dependencias de producción de Laravel
RUN composer install --no-dev --optimize-autoloader && \
    rm -rf .git node_modules tests

# Etapa 2: contenedor final limpio
FROM php:8.2-fpm-alpine

# Solo las dependencias mínimas necesarias para ejecutar Laravel
RUN apk add --no-cache \
    bash zip unzip mysql-client \
    libjpeg-turbo libpng freetype libwebp icu libzip \
    && rm -rf /var/cache/apk/*

# Copia extensiones PHP ya instaladas
COPY --from=builder /usr/local/lib/php/extensions /usr/local/lib/php/extensions
COPY --from=builder /usr/local/bin/composer /usr/local/bin/composer

# Copia app con dependencias ya instaladas
WORKDIR /var/www
COPY --from=builder /app .

EXPOSE 9000

CMD ["php-fpm"]

