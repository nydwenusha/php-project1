FROM php:8.2-apache

# Install MySQLi extension
RUN docker-php-ext-install mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy all files to Apache web root
COPY . /var/www/html/

# Set working directory to where Laravel files are
WORKDIR /var/www/html/wasantha_products

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install dependencies (composer.json is in wasantha_products/)
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www/html/wasantha_products/storage /var/www/html/wasantha_products/bootstrap/cache
RUN chmod -R 775 /var/www/html/wasantha_products/storage /var/www/html/wasantha_products/bootstrap/cache

# Set environment variables
ENV APP_ENV=production
ENV APP_DEBUG=false

# Configure Apache to serve from public directory
RUN sed -i 's!/var/www/html!/var/www/html/wasantha_products/public!g' /etc/apache2/sites-available/000-default.conf

# Create startup script to run migrations
RUN echo '#!/bin/bash\n\
cd /var/www/html/wasantha_products\n\
php artisan migrate --force\n\
apache2-foreground' > /usr/local/bin/startup.sh && \
chmod +x /usr/local/bin/startup.sh

EXPOSE 80

CMD ["/usr/local/bin/startup.sh"]
