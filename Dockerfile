FROM php:8.2-apache

# Install system dependencies and unzip
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    libpq-dev \
    && docker-php-ext-install zip

# Install MySQLi and PDO SQLite extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql pdo_sqlite pdo_pgsql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy all files to Apache web root
COPY . /var/www/html/

# Set permissions for storage and bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/inventory/storage /var/www/html/inventory/bootstrap/cache
RUN chmod -R 777 /var/www/html/inventory/storage /var/www/html/inventory/bootstrap/cache

# Create SQLite database directory and file
RUN mkdir -p /var/www/html/inventory/database && touch /var/www/html/inventory/database/database.sqlite

# Set working directory to inventory (where Laravel is)
WORKDIR /var/www/html/inventory

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install dependencies (composer.json is in inventory/)
RUN composer install --no-dev --optimize-autoloader --ignore-platform-req=ext-fileinfo

# Create storage symlink
RUN php artisan storage:link


# Set permissions for storage, bootstrap/cache, and database
RUN chown -R www-data:www-data /var/www/html/inventory/storage /var/www/html/inventory/bootstrap/cache /var/www/html/inventory/database
RUN chmod -R 775 /var/www/html/inventory/storage /var/www/html/inventory/bootstrap/cache /var/www/html/inventory/database

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
