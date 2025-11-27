# Use official PHP 8.2 image with Apache
FROM php:8.2-apache

# Enable required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory to /var/www/html
WORKDIR /var/www/html

# Copy all project files
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --ignore-platform-reqs

# Expose port 8080 for Render
EXPOSE 8080

# Start Apache
CMD ["apache2-foreground"]
