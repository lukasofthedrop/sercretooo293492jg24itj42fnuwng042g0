#!/bin/bash

# Script para corrigir problemas de permissão e diretórios do Laravel storage

echo "Criando diretórios de storage do Laravel..."

# Criar diretórios necessários
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Definir permissões corretas
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Criar arquivo de log vazio se não existir
touch storage/logs/laravel.log

# Limpar cache do Laravel
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "Diretórios de storage criados e permissões ajustadas com sucesso!"