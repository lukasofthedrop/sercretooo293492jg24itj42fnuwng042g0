#!/bin/bash

# ==================================================
# 📊 LUCRATIVABET - MONITOR DE SISTEMA
# ==================================================

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Função para verificar status
check_service() {
    local name=$1
    local check_command=$2
    
    if eval $check_command > /dev/null 2>&1; then
        echo -e "${GREEN}✅ $name${NC}"
        return 0
    else
        echo -e "${RED}❌ $name${NC}"
        return 1
    fi
}

# Loop principal
while true; do
    clear
    
    echo "=================================================="
    echo "📊 LUCRATIVABET - MONITOR DE SISTEMA"
    echo "=================================================="
    echo "🕒 $(date '+%Y-%m-%d %H:%M:%S')"
    echo "=================================================="
    echo
    
    # 1. STATUS DOS SERVIÇOS
    echo "🔧 SERVIÇOS:"
    echo "---------------------------------------------"
    
    check_service "Laravel Server" "curl -s http://localhost:8000"
    check_service "API Endpoint  " "curl -s http://localhost:8000/api | grep -q 'API is running'"
    check_service "Redis Server  " "redis-cli ping"
    
    WORKERS=$(ps aux | grep -c "[a]rtisan queue:work")
    if [ $WORKERS -gt 0 ]; then
        echo -e "${GREEN}✅ Queue Workers ($WORKERS ativos)${NC}"
    else
        echo -e "${RED}❌ Queue Workers${NC}"
    fi
    
    echo
    
    # 2. MÉTRICAS DO SISTEMA
    echo "📈 MÉTRICAS:"
    echo "---------------------------------------------"
    
    # CPU
    CPU=$(ps aux | grep "artisan serve" | grep -v grep | awk '{print $3}' | head -1)
    if [ -n "$CPU" ]; then
        echo "CPU Laravel: ${CPU}%"
    fi
    
    # Memória
    MEM=$(ps aux | grep "artisan serve" | grep -v grep | awk '{print $4}' | head -1)
    if [ -n "$MEM" ]; then
        echo "RAM Laravel: ${MEM}%"
    fi
    
    # Redis Info
    REDIS_USED=$(redis-cli INFO memory 2>/dev/null | grep used_memory_human | cut -d: -f2 | tr -d '\r')
    if [ -n "$REDIS_USED" ]; then
        echo "Redis Memory: $REDIS_USED"
    fi
    
    # Conexões Redis
    REDIS_CONN=$(redis-cli CLIENT LIST 2>/dev/null | wc -l)
    echo "Redis Conexões: $REDIS_CONN"
    
    echo
    
    # 3. LOGS RECENTES
    echo "📝 ÚLTIMOS LOGS:"
    echo "---------------------------------------------"
    
    # Últimas linhas do log do Laravel
    if [ -f storage/logs/laravel.log ]; then
        tail -n 3 storage/logs/laravel.log 2>/dev/null | while IFS= read -r line; do
            if echo "$line" | grep -q "ERROR"; then
                echo -e "${RED}$line${NC}"
            elif echo "$line" | grep -q "WARNING"; then
                echo -e "${YELLOW}$line${NC}"
            else
                echo "$line"
            fi
        done
    fi
    
    echo
    
    # 4. TESTES RÁPIDOS
    echo "🧪 HEALTH CHECKS:"
    echo "---------------------------------------------"
    
    # Response time homepage
    RESPONSE_TIME=$(curl -o /dev/null -s -w '%{time_total}' http://localhost:8000)
    echo "Homepage Response: ${RESPONSE_TIME}s"
    
    # Response time API
    API_TIME=$(curl -o /dev/null -s -w '%{time_total}' http://localhost:8000/api)
    echo "API Response: ${API_TIME}s"
    
    # Verificar se há erros 500
    ERROR_CHECK=$(curl -s -o /dev/null -w "%{http_code}" http://localhost:8000)
    if [ "$ERROR_CHECK" = "500" ]; then
        echo -e "${RED}⚠️  ERRO 500 DETECTADO!${NC}"
    fi
    
    echo
    echo "=================================================="
    echo "🔄 Atualizando em 5 segundos... (Ctrl+C para sair)"
    echo "=================================================="
    
    sleep 5
done