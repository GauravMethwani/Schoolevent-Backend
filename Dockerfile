# Use official PHP 8.2 image with Apache
FROM php:8.2-apache

# Install required PHP extensions and system packages
RUN apt-get update && apt-get install -y \
    libzip-dev unzip curl git zip libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip mbstring exif pcntl bcmath gd

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory in container
WORKDIR /var/www/html

# Copy all project files to the container
COPY . .

# Install Composer (globally)
RUN curl -sS https://getcomposer.org/installer | php \
    -- --install-dir=/usr/local/bin --filename=composer

# Install Laravel dependencies (optimized, without dev packages)
RUN composer install --no-dev --optimize-autoloader

# Generate Laravel storage folders permissions
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

RUN php artisan storage:link || true

# Expose port 8000 for Laravel dev server
EXPOSE 8000

# Start Laravel development server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
