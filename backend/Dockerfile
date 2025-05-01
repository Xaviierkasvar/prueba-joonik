FROM php:8.2-fpm

# Arguments defined in docker-compose.yml
ARG user=www-data
ARG uid=33

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

# Crear el directorio de la aplicación
WORKDIR /var/www/html

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar el código de la aplicación y establecer permisos
COPY --chown=www-data:www-data . .

# Crear estructura de directorios y establecer permisos
RUN mkdir -p \
    storage/framework/{sessions,views,cache} \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Ejecutar composer install como www-data
USER www-data
RUN composer install --no-scripts --no-autoloader

# Volver a root para finalizar la configuración
USER root

# Generar el autoloader de composer optimizado
RUN composer dump-autoload --optimize

# Asegurarnos de que www-data sea el propietario final
RUN chown -R www-data:www-data /var/www/html

# Cambiar al usuario www-data para la ejecución
USER www-data

EXPOSE 9000
CMD ["sh", "-c", "php artisan migrate:fresh --seed && php-fpm"]
