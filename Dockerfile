FROM php:8.2-apache

# 1. Install system dependencies & PHP extensions
RUN apt-get update && apt-get install -y \
    libzip-dev unzip curl ca-certificates \
    && docker-php-ext-install zip mysqli pdo pdo_mysql

# 2. Enable Apache mod_rewrite
RUN a2enmod rewrite

# 3. Create the certs folder and Download the TiDB/Let's Encrypt Root Certificate
RUN mkdir -p /etc/ssl/certs && \
    curl -o /etc/ssl/certs/tidb-ca.pem https://letsencrypt.org/certs/isrgrootx1.pem

# 4. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Set working directory
WORKDIR /var/www/html

# 6. Copy Project Files
COPY . /var/www/html/

# 7. Install PHP Dependencies (THIS IS THE FIX)
# We add COMPOSER_MEMORY_LIMIT=-1 to stop the crash
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs

# 8. Fix Permissions
RUN chown -R www-data:www-data /var/www/html

RUN ln -snf /usr/share/zoneinfo/Asia/Kolkata /etc/localtime && echo "Asia/Kolkata" > /etc/timezone

# 9. Configure Ports for Render
RUN sed -i 's/80/8080/g' /etc/apache2/ports.conf /etc/apache2/sites-enabled/000-default.conf
EXPOSE 8080

CMD ["apache2-foreground"]
