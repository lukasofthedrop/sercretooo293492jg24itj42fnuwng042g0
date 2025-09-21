#!/bin/sh
set -euo pipefail

PORT=${PORT:-8080}

# Render nginx configuration with the runtime port
envsubst '$PORT' < /app/docker/nginx.conf.template > /etc/nginx/http.d/default.conf

# Ensure Laravel storage directories have proper permissions
chown -R www-data:www-data /app/storage /app/bootstrap/cache || true
chmod -R ug+rwX /app/storage /app/bootstrap/cache || true

# Prime caches when possible
php artisan config:clear >/dev/null 2>&1 || true
php artisan cache:clear >/dev/null 2>&1 || true
php artisan config:cache >/dev/null 2>&1 || true
php artisan route:cache >/dev/null 2>&1 || true
php artisan view:cache >/dev/null 2>&1 || true
php artisan storage:link >/dev/null 2>&1 || true

exec /usr/bin/supervisord -c /app/docker/supervisord.conf
