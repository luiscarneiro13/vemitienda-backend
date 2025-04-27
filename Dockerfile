# Etapa 1: Build de dependencias
FROM php:8.2-fpm AS builder

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    nodejs \
    npm \
    default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer manualmente
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Directorio de trabajo
WORKDIR /var/www

# Copiar solo archivos necesarios
COPY .env.example .env
COPY composer.json composer.lock package.json package-lock.json webpack.mix.js ./

# Instalar dependencias backend (PHP)
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

# Instalar dependencias frontend (Node)
RUN npm install

# Copiar el resto del proyecto
COPY . .

# Copiar el .env (por si acaso)
RUN cp .env.example .env

# Etapa 2: Imagen final (runtime)
FROM php:8.2-fpm

# Instalar dependencias mínimas para producción
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    nodejs \
    npm \
    default-mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer manualmente
RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

# Directorio de trabajo
WORKDIR /var/www

# Copiar proyecto ya construido
COPY --from=builder /var/www /var/www

# Establecer permisos correctos
RUN mkdir -p storage bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Configurar usuario para correr el contenedor
USER www-data

EXPOSE 9010

CMD ["php-fpm"]
