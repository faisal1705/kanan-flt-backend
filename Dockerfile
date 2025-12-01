FROM php:8.2-apache

# -----------------------------------------------------
# Install system packages + PHP extensions
# -----------------------------------------------------
RUN apt-get update && apt-get install -y \
    libzip-dev unzip curl \
    && docker-php-ext-install zip mysqli pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# -----------------------------------------------------
# Install Composer
# -----------------------------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# -----------------------------------------------------
# Copy App Files
# -----------------------------------------------------
WORKDIR /var/www/html
COPY . .

# -----------------------------------------------------
# Install PHP dependencies
# (ignore platform reqs because Render uses a stripped-down PHP build)
# -----------------------------------------------------
RUN composer install --no-interaction --prefer-dist --ignore-platform-reqs || true

# -----------------------------------------------------
# Required for Render (Apache must run on port 8080)
# -----------------------------------------------------
EXPOSE 8080

# Change Apache from port 80 → 8080
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf \
    && sed -i 's/:80/:8080/g' /etc/apache2/sites-enabled/000-default.conf

# -----------------------------------------------------
# Start Apache
# -----------------------------------------------------
CMD ["apache2-foreground"]
