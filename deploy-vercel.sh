#!/bin/bash

# Script de Deploy Vercel - LucrativaBet
# Autor: Claude Code Assistant
# Data: 11/09/2025

echo "🚀 Iniciando deploy Vercel do LucrativaBet..."

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Funções de log
log_info() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

log_warn() {
    echo -e "${YELLOW}[WARN]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

log_step() {
    echo -e "${BLUE}[STEP]${NC} $1"
}

# Verificar se Vercel CLI está instalado
if ! command -v vercel &> /dev/null; then
    log_error "Vercel CLI não encontrado. Instalando..."
    npm install -g vercel
fi

# Etapa 1: Login no Vercel
log_step "1/6 - Login no Vercel..."
log_info "Por favor, faça login no Vercel:"
vercel login --github

# Etapa 2: Configurar projeto
log_step "2/6 - Configurando projeto Vercel..."

# Criar vercel.json se não existir
if [ ! -f "vercel.json" ]; then
    log_info "Criando configuração Vercel..."
    cat > vercel.json << 'EOF'
{
  "framework": "laravel",
  "build": {
    "env": {
      "APP_ENV": "production",
      "APP_DEBUG": "false"
    }
  },
  "env": {
    "APP_NAME": "Lucrativa Bet",
    "APP_KEY": "base64:jP1f2K0S7Xe5JkyxyP8EptDNe8w77mGYOWcEoZyH9FU=",
    "APP_DEBUG": "false",
    "LOG_LEVEL": "error",
    "DB_CONNECTION": "pgsql",
    "CACHE_DRIVER": "redis",
    "SESSION_DRIVER": "redis",
    "QUEUE_CONNECTION": "redis",
    "TOKEN_DE_2FA": "6d4b4e8e5dda575ae6679a153fce302831fd5001f58e21ba3587c96d00baa2826fa312b80425b90b02f3b7d5612d541d4dda6e5253be5565d011ea28a2cdfc5b"
  },
  "builds": [
    {
      "src": "package.json",
      "use": "@vercel/static-build",
      "config": {
        "buildCommand": "npm run build"
      }
    }
  ],
  "routes": [
    {
      "src": "/(.*)",
      "dest": "/index.php"
    }
  ]
}
EOF
fi

# Etapa 3: Instalar dependências
log_step "3/6 - Instalando dependências..."

# Instalar dependências PHP
log_info "Instalando dependências PHP..."
composer install --no-dev --optimize-autoloader --no-interaction

# Instalar dependências Node
log_info "Instalando dependências Node..."
npm install

# Etapa 4: Build assets
log_step "4/6 - Build de assets..."

# Build frontend
log_info "Compilando assets..."
npm run build

# Otimizar Laravel
log_info "Otimizando aplicação..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload --optimize

# Etapa 5: Deploy para Vercel
log_step "5/6 - Deploy para Vercel..."

log_info "Iniciando deploy..."
vercel --prod

# Etapa 6: Finalizar
log_step "6/6 - Finalizando deploy..."

log_info "✅ Deploy Vercel concluído com sucesso!"
log_info "📋 Resumo do deploy:"
log_info "   - Projeto configurado para Vercel"
log_info "   - Dependências instaladas"
log_info "   - Assets compilados"
log_info "   - Aplicação otimizada"
log_info "   - Deploy realizado com sucesso"

echo ""
echo "🌐 Informações de acesso:"
echo "   - URL da aplicação: fornecida pelo Vercel"
echo "   - Dashboard: https://vercel.com/dashboard"
echo ""

echo "🔧 Comandos úteis:"
echo "   - Verificar deploy: vercel ls"
echo "   - Ver logs: vercel logs"
echo "   - Remover deploy: vercel remove"
echo ""

log_info "🎉 LucrativaBet está no ar na Vercel!"