FROM ubuntu:latest

# Set environment variables
ENV DEBIAN_FRONTEND=noninteractive

# Update system and install necessary dependencies
RUN apt-get update && apt-get install -y \
    software-properties-common \
    curl \
    unzip \
    git \
    nginx \
    supervisor \
    php8.3 \
    php8.3-cli \
    php8.3-fpm \
    php8.3-mbstring \
    php8.3-xml \
    php8.3-curl \
    php8.3-zip \
    php8.3-mysql \
    php8.3-bcmath \
    php8.3-gd \
    php8.3-intl \
    php8.3-soap \
    php8.3-readline \
    php8.3-opcache \
    php8.3-tokenizer \
    php8.3-pdo \
    php8.3-sqlite3 \
    php8.3-redis \
    php8.3-xmlrpc \
    php8.3-exif && \
    apt-get clean

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/html

# Copy Laravel project files (pastikan berada di folder yang sesuai)
COPY . .

# Set proper permissions for Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage/logs \
    && chmod -R 775 /var/www/html/storage/framework \
    && chmod -R 775 /var/www/html/storage/framework/views \
    && chmod -R 775 /var/www/html/storage/framework/sessions \
    && chmod -R 775 /var/www/html/storage/framework/cache

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Configure Nginx
COPY laravel-nginx.conf /etc/nginx/sites-available/default
RUN rm -f /etc/nginx/sites-enabled/default && ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/default

# Configure Supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Ensure PHP-FPM runs properly
RUN mkdir -p /run/php && chown -R www-data:www-data /run/php

# Expose ports
EXPOSE 80 443

# Start services using supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf", "-n"]
