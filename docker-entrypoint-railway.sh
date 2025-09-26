#!/bin/bash
set -e

echo "=== LucrativaBet Railway Deployment Initialization ==="

# Wait for services to be ready
echo "Waiting for services to be ready..."
sleep 5

# Check if .env file exists
if [ ! -f /var/www/html/.env ]; then
    echo "ERROR: .env file not found. Please ensure it's properly configured in Railway."
    exit 1
fi

# Generate application key if not present
if grep -q "APP_KEY=base64:" /var/www/html/.env; then
    echo "Application key already set"
else
    echo "Generating application key..."
    php artisan key:generate --force --no-interaction
fi

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force --no-interaction

# Cache configuration for better performance
echo "Caching configuration..."
php artisan config:cache

# Cache routes for better performance
echo "Caching routes..."
php artisan route:cache

# Cache views for better performance
echo "Caching views..."
php artisan view:cache

# Clear any existing cache that might be stale
echo "Clearing application cache..."
php artisan cache:clear

# Create storage link if it doesn't exist
echo "Creating storage link..."
php artisan storage:link

# Ensure storage and bootstrap/cache are writable
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

# Optimize Laravel for production
echo "Optimizing Laravel for production..."
php artisan optimize:clear
php artisan optimize

# Create health check endpoint
echo "Creating health check endpoint..."
mkdir -p /var/www/html/public/health
echo '<?php
header("Content-Type: application/json");
echo json_encode([
    "status" => "healthy",
    "timestamp" => date("Y-m-d H:i:s"),
    "app" => "LucrativaBet Casino",
    "version" => "1.0.0"
]);
' > /var/www/html/public/health/index.php

echo "=== Railway initialization complete. Starting Apache ==="

# Execute the original command (start Apache)
exec apache2-foreground