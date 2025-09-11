#!/bin/bash

# Gerar APP_KEY se não existir
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Executar migrações
php artisan migrate --force

# Limpar e cachear configurações
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Criar link simbólico para storage
php artisan storage:link

# Iniciar Apache
exec "$@"