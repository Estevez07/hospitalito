# Usa una imagen base de PHP
FROM php:8.0-apache

# Instala las dependencias de PostgreSQL
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copia el código de tu aplicación al contenedor
COPY . /var/www/html/

# Exponer el puerto necesario
EXPOSE 80

