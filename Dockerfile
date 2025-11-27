FROM php:8.2-apache

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    libpng-dev \
    curl \
    && docker-php-ext-install zip mysqli pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy project files
COPY . /var/www/html/

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Run composer install
RUN composer install --no-interaction --prefer-dist --ignore-platform-reqs

# Expose Render port
EXPOSE 8080

# Apache runs on port 8080 for Render
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf

CMD ["apache2-foreground"]
