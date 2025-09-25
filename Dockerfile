FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libicu-dev \
    nano \
    && docker-php-ext-install pdo_mysql mbstring zip intl \
    && docker-php-ext-enable pdo_mysql mbstring zip intl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy Composer from official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache modules
RUN a2enmod rewrite headers ssl

# Copy application files
COPY . /var/www/html

# Ensure .env.example exists (force copy)
COPY .env.example /var/www/html/.env.example

# Create necessary directories with correct permissions
RUN mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/bootstrap/cache

# Configure Apache VirtualHost with better configuration
RUN echo '<VirtualHost *:80>\n\
    ServerName localhost\n\
    ServerAlias *\n\
    DocumentRoot /var/www/html/public\n\
    \n\
    <Directory /var/www/html>\n\
        Options -Indexes\n\
        AllowOverride None\n\
    </Directory>\n\
    \n\
    <Directory /var/www/html/public>\n\
        Options -Indexes\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    \n\
    # Security headers\n\
    Header always set X-Content-Type-Options nosniff\n\
    Header always set X-Frame-Options DENY\n\
    Header always set X-XSS-Protection "1; mode=block"\n\
    \n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set proper ownership and permissions
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type f -exec chmod 644 {} \; \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Copy and setup entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port
EXPOSE 80

# Use entrypoint
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
