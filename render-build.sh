#!/usr/bin/env bash
# exit on error
set -o errexit

echo "🚀 Iniciando build para Render..."

# Instalar dependências PHP
echo "📦 Instalando dependências PHP..."
composer install --optimize-autoloader --no-dev

# Instalar dependências Node
echo "📦 Instalando dependências Node..."
npm ci

# Compilar assets
echo "🎨 Compilando assets..."
npm run build

# Limpar caches
echo "🧹 Limpando caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Gerar caches otimizados
echo "⚡ Otimizando aplicação..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Criar link simbólico para storage
echo "🔗 Criando link para storage..."
php artisan storage:link || true

# Executar migrações
echo "🗄️ Executando migrações..."
php artisan migrate --force

# Seed database (apenas se necessário)
# php artisan db:seed --force

echo "✅ Build concluído com sucesso!"