#!/bin/bash
set -e

echo "=== Laravel Application Initialization ==="

# Wait for a moment to ensure the environment is ready
sleep 2

# Create .env file if it doesn't exist
if [ ! -f /var/www/html/.env ]; then
    echo "Creating .env file from .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
else
    echo ".env file already exists"
fi

# Generate application key if not present
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "Generating application key..."
    php artisan key:generate --force --no-interaction
else
    echo "Application key already set"
fi

# Cache configuration for better performance
echo "Caching configuration..."
php artisan config:cache

# Cache routes for better performance
echo "Caching routes..."
php artisan route:cache

# Clear any existing cache that might be stale
echo "Clearing application cache..."
php artisan cache:clear

# Ensure storage and bootstrap/cache are writable
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

echo "=== Initialization complete. Starting Apache ==="

# Execute the original command (start Apache)
exec apache2-foreground