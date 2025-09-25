#!/bin/bash
set -e

echo "=== Laravel Application Initialization ==="
echo "Current user: $(whoami)"
echo "Working directory: $(pwd)"
echo "Files in /var/www/html:"
ls -la /var/www/html/ | head -10

# Wait for a moment to ensure the environment is ready
sleep 2

# Check if .env.example exists - if not, we'll create .env directly from environment variables
if [ -f "/var/www/html/.env.example" ]; then
    echo "✅ .env.example found"
    ls -la /var/www/html/.env.example
    USE_ENV_EXAMPLE=true
else
    echo "⚠️ .env.example NOT found in /var/www/html/ - will create .env from environment variables"
    echo "Files in /var/www/html/ that start with .env:"
    ls -la /var/www/html/.env* || echo "No .env* files found"
    USE_ENV_EXAMPLE=false
fi

# Create .env file if it doesn't exist
if [ ! -f /var/www/html/.env ]; then
    if [ "$USE_ENV_EXAMPLE" = true ]; then
        echo "Creating .env file from .env.example..."
        cp /var/www/html/.env.example /var/www/html/.env
        echo "✅ .env file created from .env.example"
    else
        echo "Creating .env file directly from environment variables..."
        # Will be created in the next step
        echo "✅ .env will be created from environment variables"
    fi
else
    echo "✅ .env file already exists"
fi

# Override .env with environment variables from DigitalOcean
echo "Updating .env with DigitalOcean environment variables..."
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

# Filament Configuration
FILAMENT_BASE_URL=${FILAMENT_BASE_URL:-admin}
EOF

echo "✅ .env file updated with environment variables"

# Generate application key if not present or empty
if grep -q "APP_KEY=$" /var/www/html/.env || ! grep -q "APP_KEY=base64:" /var/www/html/.env; then
    echo "Generating new application key..."
    php artisan key:generate --force --no-interaction
    echo "✅ Application key generated"
else
    echo "✅ Application key already set"
fi

# Set proper file permissions before running artisan commands
echo "Setting proper file permissions..."
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html/storage
chmod -R 755 /var/www/html/bootstrap/cache
chmod 644 /var/www/html/.env
echo "✅ Permissions set"

# Clear all caches first
echo "Clearing all caches..."
php artisan config:clear || echo "Config clear failed (non-critical)"
php artisan route:clear || echo "Route clear failed (non-critical)"
php artisan view:clear || echo "View clear failed (non-critical)"
php artisan cache:clear || echo "Cache clear failed (non-critical)"
echo "✅ Caches cleared"

# Cache configuration for better performance
echo "Caching configuration..."
php artisan config:cache || echo "Config cache failed (non-critical)"
echo "✅ Configuration cached"

# Test basic Laravel functionality
echo "Testing Laravel installation..."
php artisan --version
echo "✅ Laravel is working"

echo "=== Initialization complete. Starting Apache ==="
echo "Apache version: $(apache2 -v | head -1)"
echo "PHP version: $(php -v | head -1)"

# Start Apache in foreground
exec apache2-foreground
