#!/bin/sh
set -euo pipefail

PORT=${PORT:-8080}
ROLE=${APP_ROLE:-web}

if [ "$ROLE" = "web" ]; then
    # Render nginx configuration with the runtime port
    envsubst '$PORT' < /app/docker/nginx.conf.template > /etc/nginx/http.d/default.conf

    # Ensure VIEW_COMPILED_PATH is stable and directories exist
    export VIEW_COMPILED_PATH="/app/storage/framework/views"
    mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache || true
    touch storage/logs/laravel.log || true

    # Ensure affiliate CSS asset exists (serve 200 on /css/custom-filament-theme-affiliate.css)
    mkdir -p /app/public/css || true
    if [ ! -f /app/public/css/custom-filament-theme-affiliate.css ]; then
        if [ -f /app/public/css/custom-filament-theme.css ]; then
            cp /app/public/css/custom-filament-theme.css /app/public/css/custom-filament-theme-affiliate.css || true
        else
            echo "/* fallback affiliate css */" > /app/public/css/custom-filament-theme-affiliate.css || true
        fi
    fi
    echo "=== Public CSS Directory ===" >&2
    ls -la /app/public/css >&2 || true

    # Extract public storage assets if archive exists and hasn't been unpacked yet
    if [ -f /app/storage_public.tar.xz ] && [ ! -f /app/storage/.public_extracted ]; then
        tar -xJf /app/storage_public.tar.xz -C /app
        touch /app/storage/.public_extracted
    fi

    # Ensure Laravel storage directories have proper permissions
    chown -R www-data:www-data /app/storage /app/bootstrap/cache || true
    chmod -R ug+rwX /app/storage /app/bootstrap/cache || true

    # Optionally run outstanding migrations when explicitly requested
    if [ "${RUN_MIGRATIONS:-0}" = "1" ]; then
        php artisan migrate --force >/dev/null 2>&1 || true
    fi

    # Optionally seed test/admin users once when requested
    if [ "${RUN_SEEDERS:-0}" = "1" ]; then
        if [ ! -f /app/storage/.seeded_test_users ]; then
            php artisan db:seed --class=Database\\Seeders\\TestUsersSeeder >/dev/null 2>&1 || true
            touch /app/storage/.seeded_test_users || true
        fi
    fi

    # Prime caches when possible
    php artisan package:discover >/dev/null 2>&1 || true
    php artisan config:clear >/dev/null 2>&1 || true
    php artisan cache:clear >/dev/null 2>&1 || true
    php artisan config:cache >/dev/null 2>&1 || true
    php artisan route:clear >/dev/null 2>&1 || true
    php artisan view:clear >/dev/null 2>&1 || true
    php artisan view:cache >/dev/null 2>&1 || true

    # Ensure the public storage symlink exists even if a real folder slipped into the repo
    if [ ! -L public/storage ]; then
        rm -rf public/storage >/dev/null 2>&1 || true
    fi
    ln -snf /app/storage/app/public /app/public/storage >/dev/null 2>&1 || true
    php artisan storage:link >/dev/null 2>&1 || true

    exec /usr/bin/supervisord -c /app/docker/supervisord.conf
fi

if [ "$ROLE" = "queue" ]; then
    exec php artisan queue:work ${QUEUE_WORK_OPTIONS:---sleep=3 --tries=3 --max-time=3600}
fi

if [ "$ROLE" = "scheduler" ]; then
    INTERVAL=${SCHEDULER_INTERVAL:-60}
    while true; do
        php artisan schedule:run --verbose --no-interaction || true
        sleep "$INTERVAL"
    done
fi

# Fallback: execute any custom command that was provided
exec "$@"
