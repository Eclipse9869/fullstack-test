# Gunakan image PHP + Apache
FROM php:8.2-apache 

# Install dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip curl git libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy project ke container
COPY . /var/www/html/

# Aktifkan mod_rewrite
RUN a2enmod rewrite

# Atur permission
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Salin file konfigurasi apache custom (opsional)
# COPY ./apache-config.conf /etc/apache2/sites-available/000-default.conf

# Set working directory
WORKDIR /var/www/html