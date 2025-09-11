#!/usr/bin/env bash
# exit on error
set -o errexit

# Instalar dependências PHP
composer install --no-dev --optimize-autoloader

# Instalar dependências Node
npm ci
npm run build

# Gerar APP_KEY se não existir
php artisan key:generate --force

# Executar migrações
php artisan migrate --force

# Limpar e cachear configurações
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Criar link simbólico para storage
php artisan storage:link

# Configurar permissões
chmod -R 755 storage bootstrap/cache

echo "Build completo!"