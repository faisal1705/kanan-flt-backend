FROM php:8.2-apache

# Install required tools + PHP extensions (mysqli etc)
RUN apt-get update && apt-get install -y \
    libzip-dev unzip libpng-dev curl pkg-config libssl-dev \
    && docker-php-ext-install zip mysqli pdo pdo_mysql

# Install pecl and mongodb extension dependencies
RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy project files
COPY . /var/www/html/

# Install Composer (copy from official composer image)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Run composer install (make sure composer.json exists)
RUN composer install --no-interaction --prefer-dist --ignore-platform-reqs

# Expose Render port
EXPOSE 8080

# Fix Apache port for Render
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf

CMD ["apache2-foreground"]
