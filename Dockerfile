FROM php:8.2-cli-alpine

WORKDIR /app

RUN apk add --no-cache zlib-dev libpng-dev \
    && docker-php-ext-install pdo pdo_mysql gd

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

ENTRYPOINT ["composer"]
