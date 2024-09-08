# Usa una imagen oficial de PHP
FROM php:8.0-apache

# Copia todo el contenido de tu proyecto al contenedor
COPY . /var/www/html/

# Exponer el puerto 80 (para HTTP)
EXPOSE 80

# Habilitar mod_rewrite de Apache si tu proyecto lo necesita
RUN a2enmod rewrite

# Instala extensiones de PHP que puedas necesitar (ejemplo: PDO para PostgreSQL)
RUN docker-php-ext-install pdo pdo_pgsql
