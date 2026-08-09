FROM php:8.2-apache

# Install system dependencies and unzip
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-install zip

# Install MySQLi extension
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy all files to Apache web root
COPY . /var/www/html/

# Force fresh build
RUN echo "Force rebuild on $(date)"

# Set working directory to inventory folder (where Laravel is)
WORKDIR /var/www/html/inventory

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install dependencies (composer.json is in inventory/)
RUN composer install --no-dev --optimize-autoloader --ignore-platform-req=ext-fileinfo

# Set permissions
RUN chown -R www-data:www-data /var/www/html/inventory/storage /var/www/html/inventory/bootstrap/cache
RUN chmod -R 775 /var/www/html/inventory/storage /var/www/html/inventory/bootstrap/cache

# Set environment variables
ENV APP_ENV=production
ENV APP_DEBUG=false

# Configure Apache to serve from public directory
RUN sed -i 's!/var/www/html!/var/www/html/inventory/public!g' /etc/apache2/sites-available/000-default.conf

# Create startup script to run migrations
RUN echo '#!/bin/bash\n\
cd /var/www/html/inventory\n\
php artisan migrate:fresh --force\n\
apache2-foreground' > /usr/local/bin/startup.sh && \
chmod +x /usr/local/bin/startup.sh

EXPOSE 80

CMD ["/usr/local/bin/startup.sh"]
