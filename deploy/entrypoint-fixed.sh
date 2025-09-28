#!/bin/sh
set -euo pipefail

PORT=${PORT:-8080}
ROLE=${APP_ROLE:-web}

if [ "$ROLE" = "web" ]; then
    # Render nginx configuration with the runtime port
    envsubst '$PORT' < /app/docker/nginx.conf.template > /etc/nginx/http.d/default.conf
    
    # Set VIEW_COMPILED_PATH environment variable to Laravel storage path
    export VIEW_COMPILED_PATH="/app/storage/framework/views"

    # Extract public storage assets if archive exists and hasn't been unpacked yet
    if [ -f /app/storage_public.tar.xz ] && [ ! -f /app/storage/.public_extracted ]; then
        tar -xJf /app/storage_public.tar.xz -C /app
        touch /app/storage/.public_extracted
    fi

    # Ensure Laravel storage directories exist and have proper permissions
    mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache /tmp/views
    touch storage/logs/laravel.log
    chown -R www-data:www-data /app/storage /app/bootstrap/cache /tmp/views || true
    chmod -R ug+rwX /app/storage /app/bootstrap/cache /tmp/views || true
    
    # Log directory creation status
    echo "=== Storage Directory Status ===" >&2
    ls -la /app/storage/framework/ >&2
    echo "=== storage/framework/views Directory Status ===" >&2
    ls -la /app/storage/framework/views >&2
    echo "=== View Compiled Path ===" >&2
    echo "VIEW_COMPILED_PATH: ${VIEW_COMPILED_PATH}" >&2
    
    # Verify the view cache directory exists and is writable
    php artisan view:clear >/dev/null 2>&1 || true

    # Optionally run outstanding migrations when explicitly requested
    if [ "${RUN_MIGRATIONS:-0}" = "1" ]; then
        php artisan migrate --force >/dev/null 2>&1 || true
    fi

    # Clear all caches
    php artisan cache:clear >/dev/null 2>&1 || true
    php artisan config:clear >/dev/null 2>&1 || true
    php artisan route:clear >/dev/null 2>&1 || true
    php artisan view:clear >/dev/null 2>&1 || true

    # Prime caches when possible
    php artisan package:discover >/dev/null 2>&1 || true
    php artisan config:cache >/dev/null 2>&1 || true
    php artisan route:cache >/dev/null 2>&1 || true
    php artisan view:cache >/dev/null 2>&1 || true

    # Optionally create safe test users once for production debug/logins
    if [ "${CREATE_TEST_USERS_ON_BOOT:-0}" = "1" ] && [ ! -f /app/storage/.test_users_seeded ]; then
        echo "=== Creating test users (one-time) ===" >&2
        php artisan user:create-affiliate >/dev/null 2>&1 || true
        touch /app/storage/.test_users_seeded || true
    fi

    # Optional resets for credentials (idempotent; re-run if password changes or forced)
    if [ -n "${ADMIN_RESET_EMAIL:-}" ] && [ -n "${ADMIN_RESET_PASSWORD:-}" ]; then
        ADMIN_KEY_SAFE="${ADMIN_RESET_EMAIL//[^A-Za-z0-9_]/_}"
        ADMIN_PASS_HASH=$(printf "%s" "$ADMIN_RESET_PASSWORD" | sha1sum | awk '{print $1}')
        KEY="/app/storage/.reset_admin_${ADMIN_KEY_SAFE}_${ADMIN_PASS_HASH}"
        if [ "${ADMIN_RESET_FORCE:-0}" = "1" ] || [ ! -f "$KEY" ]; then
            php artisan user:reset-password "$ADMIN_RESET_EMAIL" "$ADMIN_RESET_PASSWORD" --role=admin >/dev/null 2>&1 || true
            rm -f "/app/storage/.reset_admin_${ADMIN_KEY_SAFE}_"* >/dev/null 2>&1 || true
            touch "$KEY" || true
        fi
    fi
    if [ -n "${AFFILIATE_RESET_EMAIL:-}" ] && [ -n "${AFFILIATE_RESET_PASSWORD:-}" ]; then
        AFF_KEY_SAFE="${AFFILIATE_RESET_EMAIL//[^A-Za-z0-9_]/_}"
        AFF_PASS_HASH=$(printf "%s" "$AFFILIATE_RESET_PASSWORD" | sha1sum | awk '{print $1}')
        KEY="/app/storage/.reset_aff_${AFF_KEY_SAFE}_${AFF_PASS_HASH}"
        if [ "${AFFILIATE_RESET_FORCE:-0}" = "1" ] || [ ! -f "$KEY" ]; then
            php artisan user:reset-password "$AFFILIATE_RESET_EMAIL" "$AFFILIATE_RESET_PASSWORD" --role=affiliate >/dev/null 2>&1 || true
            rm -f "/app/storage/.reset_aff_${AFF_KEY_SAFE}_"* >/dev/null 2>&1 || true
            touch "$KEY" || true
        fi
    fi

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
