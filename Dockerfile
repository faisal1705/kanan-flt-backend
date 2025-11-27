# Use official PHP image
FROM php:8.2

# Install required PHP extensions (PDO + MySQL)
RUN docker-php-ext-install pdo pdo_mysql

# Set working directory inside the container
WORKDIR /app

# Copy everything from your repo to the container
COPY . .

# Install composer inside the container
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP dependencies (will auto-create vendor/)
RUN composer install --no-interaction --prefer-dist

# Expose port 8080 for Render
EXPOSE 8080

# Start PHP built-in web server
CMD ["php", "-S", "0.0.0.0:8080", "-t", "."]
