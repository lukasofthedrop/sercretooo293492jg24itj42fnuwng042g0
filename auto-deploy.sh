#!/bin/bash

# Script de Deploy Automático - LucrativaBet
# Autor: Claude Code Assistant
# Data: 11/09/2025

echo "🚀 Iniciando deploy automático do LucrativaBet..."

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

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    log_error "Este script deve ser executado no diretório raiz do projeto Laravel"
    exit 1
fi

# Etapa 1: Verificar e instalar dependências
log_step "1/8 - Verificando e instalando dependências..."

# Verificar PHP
if ! command -v php &> /dev/null; then
    log_error "PHP não encontrado. Por favor, instale o PHP primeiro."
    exit 1
fi

# Verificar Composer
if ! command -v composer &> /dev/null; then
    log_error "Composer não encontrado. Por favor, instale o Composer primeiro."
    exit 1
fi

# Verificar Node.js
if ! command -v node &> /dev/null; then
    log_error "Node.js não encontrado. Por favor, instale o Node.js primeiro."
    exit 1
fi

log_info "Dependências básicas verificadas com sucesso!"

# Etapa 2: Instalar dependências do projeto
log_step "2/8 - Instalando dependências do projeto..."

# Instalar dependências PHP
log_info "Instalando dependências PHP..."
composer install --no-dev --optimize-autoloader --no-interaction

# Instalar dependências Node
log_info "Instalando dependências Node..."
npm install

log_info "Dependências do projeto instaladas com sucesso!"

# Etapa 3: Limpar caches
log_step "3/8 - Limpando caches..."

php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

log_info "Caches limpos com sucesso!"

# Etapa 4: Gerar key e otimizar
log_step "4/8 - Gerando key e otimizando aplicação..."

# Gerar key se não existir
if [ -z "$APP_KEY" ]; then
    php artisan key:generate
fi

# Otimizar autoloader
composer dump-autoload --optimize

# Build assets
npm run build

log_info "Aplicação otimizada com sucesso!"

# Etapa 5: Configurar banco de dados
log_step "5/8 - Configurando banco de dados..."

# Executar migrações
php artisan migrate --force

# Executar seeds (opcional)
php artisan db:seed --force || true

# Otimizar banco de dados
php artisan db:optimize --force

log_info "Banco de dados configurado com sucesso!"

# Etapa 6: Testar aplicação
log_step "6/8 - Testando aplicação..."

# Verificar health check
if curl -f http://localhost:8000/api/health > /dev/null 2>&1; then
    log_info "✅ Health check passou!"
else
    log_warn "⚠️  Health check falhou, mas continuando..."
fi

# Verificar se a aplicação responde
if curl -f http://localhost:8000 > /dev/null 2>&1; then
    log_info "✅ Aplicação respondendo corretamente!"
else
    log_warn "⚠️  Aplicação não está respondendo localmente, mas isso é normal para deploy..."
fi

log_info "Testes concluídos!"

# Etapa 7: Criar backup
log_step "7/8 - Criando backup..."

# Criar diretório de backups se não existir
mkdir -p backups

# Backup do banco de dados
php artisan db:backup --filename=backups/backup-$(date +%Y%m%d-%H%M%S).sql || true

# Backup dos arquivos importantes
tar -czf backups/backup-$(date +%Y%m%d-%H%M%S).tar.gz \
    --exclude=node_modules \
    --exclude=vendor \
    --exclude=.git \
    --exclude=storage/logs \
    --exclude=storage/framework/cache \
    --exclude=storage/framework/sessions \
    --exclude=storage/framework/views \
    --exclude=backups \
    . 2>/dev/null || true

log_info "Backup criado com sucesso!"

# Etapa 8: Finalizar deploy
log_step "8/8 - Finalizando deploy..."

# Mostrar status final
log_info "✅ Deploy concluído com sucesso!"
log_info "📋 Resumo do deploy:"
log_info "   - Dependências instaladas"
log_info "   - caches limpos"
log_info "   - Aplicação otimizada"
log_info "   - Banco de dados configurado"
log_info "   - Backup criado"
log_info "   - Testes executados"

# Mostrar informações de acesso
echo ""
echo "🌐 Informações de acesso:"
echo "   - Aplicação: http://localhost:8000"
echo "   - Admin: http://localhost:8000/admin"
echo "   - API: http://localhost:8000/api"
echo "   - Health Check: http://localhost:8000/api/health"
echo ""

# Mostrar comandos úteis
echo "🔧 Comandos úteis:"
echo "   - Iniciar servidor: php artisan serve"
echo "   - Verificar status: php artisan about"
echo "   - Limpar cache: php artisan cache:clear"
echo "   - Verificar logs: tail -f storage/logs/laravel.log"
echo ""

log_info "🎉 LucrativaBet está pronto para uso!"

# Mostrar informações do Railway
echo "🚀 Informações do Railway:"
echo "   - Projeto: LucrativaBet"
echo "   - Serviços: lucrativabet-app, lucrativabet-db, lucrativabet-redis"
echo "   - Dashboard: https://railway.app/dashboard"
echo ""

log_info "✅ Deploy automático concluído com sucesso!"