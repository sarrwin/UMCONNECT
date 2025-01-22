# Use the official PHP image as a base image
FROM php:8.2.12-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    curl \
    vim && \
    docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath gd && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory
WORKDIR /var/www/html

# Copy existing application code
COPY . .

# Copy the .env file
COPY .env .env

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions for storage and bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Expose the port Laravel uses
EXPOSE 8000

# Skip database-dependent commands during the build process

# Run Laravel's server
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
