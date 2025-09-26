#!/bin/bash
set -e

echo "=== Laravel Application Initialization ==="

# Wait for a moment to ensure the environment is ready
sleep 2

# Create .env file if it doesn't exist
if [ ! -f /var/www/html/.env ]; then
    echo "Creating .env file..."
    if [ -f "/var/www/html/.env.example" ]; then
        echo "Using .env.example as template"
        cp /var/www/html/.env.example /var/www/html/.env
    else
        echo ".env.example not found, creating from environment variables"
        # Create .env from environment variables provided by DigitalOcean
        cat > /var/www/html/.env << EOF
APP_NAME=${APP_NAME:-LucrativaBet}
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY:-}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-https://lucrativabet-casino-59r48.ondigitalocean.app}
LOG_CHANNEL=${LOG_CHANNEL:-stack}
LOG_LEVEL=${LOG_LEVEL:-error}

DB_CONNECTION=${DB_CONNECTION:-mysql}
DB_HOST=${DB_HOST:-127.0.0.1}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-lucrativabet}
DB_USERNAME=${DB_USERNAME:-root}
DB_PASSWORD=${DB_PASSWORD:-}

BROADCAST_DRIVER=${BROADCAST_DRIVER:-log}
CACHE_DRIVER=${CACHE_DRIVER:-file}
FILESYSTEM_DISK=${FILESYSTEM_DISK:-local}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-sync}
SESSION_DRIVER=${SESSION_DRIVER:-file}
SESSION_LIFETIME=${SESSION_LIFETIME:-120}

JWT_SECRET=${JWT_SECRET:-}
JWT_TTL=${JWT_TTL:-60}
EOF
        echo "Created .env from environment variables"
    fi
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
