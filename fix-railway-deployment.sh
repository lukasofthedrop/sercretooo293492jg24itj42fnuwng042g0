#!/bin/bash

echo "=== Fixing Railway Deployment Issues ==="

# This script should be run in the Railway environment or during deployment

# 1. Ensure the VIEW_COMPILED_PATH is set correctly
export VIEW_COMPILED_PATH="/app/storage/framework/views"

# 2. Create the necessary directories
mkdir -p /app/storage/framework/views
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

# 3. Set proper permissions
chown -R www-data:www-data /app/storage/framework/views storage bootstrap/cache
chmod -R 755 /app/storage/framework/views storage bootstrap/cache

# 4. Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 5. Regenerate caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 6. Ensure the storage link exists
php artisan storage:link

echo "=== Railway Deployment Fix Complete ==="
