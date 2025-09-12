#!/bin/bash

# Script de Deploy Completo - LucrativaBet
# Autor: Claude Code Assistant
# Data: 11/09/2025

echo "🚀 Iniciando deploy completo do LucrativaBet..."

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
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

# Função para ler input sem travar
read_choice() {
    read -p "Escolha uma opção: " choice
    echo ""
}

# Verificar pré-requisitos
check_prerequisites() {
    log_info "Verificando pré-requisitos..."
    
    # Verificar PHP
    if ! command -v php &> /dev/null; then
        log_error "PHP não encontrado. Instalando..."
        brew install php
    fi
    
    # Verificar Composer
    if ! command -v composer &> /dev/null; then
        log_error "Composer não encontrado. Instalando..."
        brew install composer
    fi
    
    # Verificar Node.js
    if ! command -v node &> /dev/null; then
        log_error "Node.js não encontrado. Instalando..."
        brew install node
    fi
    
    # Verificar npm
    if ! command -v npm &> /dev/null; then
        log_error "npm não encontrado. Instalando..."
        brew install npm
    fi
    
    log_info "Pré-requisitos verificados com sucesso!"
}

# Otimizar aplicação
optimize_app() {
    log_info "Otimizando aplicação..."
    
    # Limpar caches
    php artisan cache:clear
    php artisan config:clear
    php artisan view:clear
    php artisan route:clear
    
    # Otimizar autoloader
    composer dump-autoload --optimize
    
    # Gerar key se não existir
    if [ -z "$APP_KEY" ]; then
        php artisan key:generate
    fi
    
    # Instalar dependências Node
    npm install
    
    # Build assets
    npm run build
    
    log_info "Aplicação otimizada com sucesso!"
}

# Configurar banco de dados
setup_database() {
    log_info "Configurando banco de dados..."
    
    # Executar migrações
    php artisan migrate --force
    
    # Executar seeds
    php artisan db:seed --force
    
    # Otimizar banco de dados
    php artisan db:optimize --force
    
    log_info "Banco de dados configurado com sucesso!"
}

# Testar aplicação
test_app() {
    log_info "Testando aplicação..."
    
    # Verificar health check
    if curl -f http://localhost:8000/api/health > /dev/null 2>&1; then
        log_info "Health check passou!"
    else
        log_warn "Health check falhou, continuando mesmo assim..."
    fi
    
    # Verificar se a aplicação responde
    if curl -f http://localhost:8000 > /dev/null 2>&1; then
        log_info "Aplicação respondendo corretamente!"
    else
        log_error "Aplicação não está respondendo!"
        exit 1
    fi
}

# Backup
create_backup() {
    log_info "Criando backup..."
    
    # Backup do banco de dados
    php artisan db:backup --filename=backup-$(date +%Y%m%d-%H%M%S).sql
    
    # Backup dos arquivos
    tar -czf backup-$(date +%Y%m%d-%H%M%S).tar.gz \
        --exclude=node_modules \
        --exclude=vendor \
        --exclude=.git \
        --exclude=storage/logs \
        --exclude=storage/framework/cache \
        --exclude=storage/framework/sessions \
        --exclude=storage/framework/views \
        .
    
    log_info "Backup criado com sucesso!"
}

# Deploy em produção
deploy_production() {
    log_info "Iniciando deploy em produção..."
    
    # Parar serviços se necessário
    if command -v pm2 &> /dev/null; then
        pm2 stop lucrativabet || true
    fi
    
    # Pull do repositório
    git pull origin main
    
    # Instalar dependências
    composer install --no-dev --optimize-autoloader
    
    # Otimizar aplicação
    optimize_app
    
    # Configurar banco de dados
    setup_database
    
    # Iniciar serviços
    if command -v pm2 &> /dev/null; then
        pm2 start ecosystem.config.js || pm2 start "php artisan serve --host=0.0.0.0 --port=8000" --name lucrativabet
    fi
    
    log_info "Deploy em produção concluído com sucesso!"
}

# Menu de opções
show_menu() {
    echo ""
    echo "🎯 LucrativaBet - Script de Deploy"
    echo ""
    echo "1. 🚀 Deploy Completo (Recomendado)"
    echo "2. 🔧 Otimizar Aplicação"
    echo "3. 💾 Configurar Banco de Dados"
    echo "4. 🧪 Testar Aplicação"
    echo "5. 📦 Criar Backup"
    echo "6. 🌐 Deploy em Produção"
    echo "7. 🔍 Verificar Status"
    echo "8. 📊 Monitorar Performance"
    echo "9. 🚪 Sair"
    echo ""
    read_choice
}

# Verificar status
check_status() {
    log_info "Verificando status do sistema..."
    
    # Verificar se o servidor está rodando
    if pgrep -f "php artisan serve" > /dev/null; then
        log_info "Servidor Laravel está rodando"
    else
        log_warn "Servidor Laravel não está rodando"
    fi
    
    # Verificar uso de memória
    log_info "Uso de memória: $(free -h | grep Mem | awk '{print $3 "/" $2}')"
    
    # Verificar uso de disco
    log_info "Uso de disco: $(df -h . | tail -1 | awk '{print $3 "/" $2 " (" $5 ")"}')"
    
    # Verificar PHP version
    log_info "Versão do PHP: $(php -v | head -1)"
    
    # Verificar Laravel version
    log_info "Versão do Laravel: $(php artisan --version)"
}

# Monitorar performance
monitor_performance() {
    log_info "Monitorando performance..."
    
    # Verificar tempo de resposta
    response_time=$(curl -o /dev/null -s -w '%{time_total}' http://localhost:8000)
    log_info "Tempo de resposta: ${response_time}s"
    
    # Verificar uso de CPU
    cpu_usage=$(top -l 1 | grep "CPU usage" | awk '{print $3}' | sed 's/%//')
    log_info "Uso de CPU: ${cpu_usage}%"
    
    # Verificar memória
    mem_usage=$(top -l 1 | grep "PhysMem" | awk '{print $2}' | sed 's/M//')
    log_info "Uso de memória: ${mem_usage}MB"
    
    # Verificar espaço em disco
    disk_usage=$(df -h . | tail -1 | awk '{print $5}' | sed 's/%//')
    log_info "Uso de disco: ${disk_usage}%"
}

# Execução principal
main() {
    log_info "Bem-vindo ao LucrativaBet Deploy Script!"
    
    check_prerequisites
    
    while true; do
        show_menu
        
        case $choice in
            1)
                log_info "Executando deploy completo..."
                optimize_app
                setup_database
                test_app
                create_backup
                log_info "Deploy completo finalizado!"
                ;;
            2)
                optimize_app
                ;;
            3)
                setup_database
                ;;
            4)
                test_app
                ;;
            5)
                create_backup
                ;;
            6)
                deploy_production
                ;;
            7)
                check_status
                ;;
            8)
                monitor_performance
                ;;
            9)
                log_info "Saindo... Até logo! 🚀"
                exit 0
                ;;
            *)
                log_error "Opção inválida!"
                ;;
        esac
        
        echo ""
        read -p "Pressione Enter para continuar..." dummy
    done
}

# Executar script
main "$@"