#!/bin/bash

# Script de Teste Pós-Deploy do LucrativaBet
# Este script verifica se o deployment foi bem sucedido

echo "🚀 Iniciando testes pós-deploy do LucrativaBet..."
echo "================================================"

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Função para testar URL
test_url() {
    local url=$1
    local name=$2
    local timeout=30
    
    echo -n "Testando $name... "
    
    if curl -s -o /dev/null -w "%{http_code}" --max-time $timeout $url | grep -q "200\|301\|302"; then
        echo -e "${GREEN}✅ OK${NC}"
        return 0
    else
        echo -e "${RED}❌ FALHOU${NC}"
        return 1
    fi
}

# Função para testar API específica
test_api() {
    local url=$1
    local name=$2
    local expected_field=$3
    
    echo -n "Testando API $name... "
    
    response=$(curl -s --max-time 10 $url 2>/dev/null)
    if [[ $? -eq 0 && "$response" == *"$expected_field"* ]]; then
        echo -e "${GREEN}✅ OK${NC}"
        return 0
    else
        echo -e "${RED}❌ FALHOU${NC}"
        return 1
    fi
}

# URLs para testar (substitua com a URL real do seu deploy)
BASE_URL="https://seu-deploy-url.com"
ADMIN_URL="$BASE_URL/admin"
API_URL="$BASE_URL/api/health"

echo "📡 Aguardando deployment ficar online..."
echo "    (Pressione Ctrl+C para cancelar)"

# Loop de teste
while true; do
    echo ""
    echo "$(date): Iniciando ciclo de testes..."
    
    # Testar página principal
    if test_url "$BASE_URL" "Página Principal"; then
        # Testar painel admin
        if test_url "$ADMIN_URL" "Painel Admin"; then
            # Testar API
            if test_api "$API_URL" "API Health" "status"; then
                echo ""
                echo -e "${GREEN}🎉 TODOS OS TESTES PASSARAM!${NC}"
                echo "📍 Sistema LucrativaBet 100% OPERACIONAL"
                echo ""
                echo "🔗 Links úteis:"
                echo "   - Site: $BASE_URL"
                echo "   - Admin: $ADMIN_URL"
                echo "   - API: $API_URL"
                echo ""
                echo "✅ Deploy concluído com sucesso!"
                break
            fi
        fi
    fi
    
    echo -e "${YELLOW}⏳ Aguardando 30 segundos antes do próximo teste...${NC}"
    sleep 30
done

echo ""
echo "🔍 Testes adicionais de segurança..."
echo "================================"

# Testar headers de segurança
echo "Testando headers de segurança..."
headers=$(curl -s -I "$BASE_URL" 2>/dev/null)

if echo "$headers" | grep -q "Content-Security-Policy"; then
    echo -e "${GREEN}✅ CSP Presente${NC}"
else
    echo -e "${YELLOW}⚠️  CSP não detectado${NC}"
fi

if echo "$headers" | grep -q "X-Frame-Options"; then
    echo -e "${GREEN}✅ X-Frame-Options Presente${NC}"
else
    echo -e "${YELLOW}⚠️  X-Frame-Options não detectado${NC}"
fi

echo ""
echo -e "${GREEN}🚀 Deploy LucrativaBet finalizado com sucesso!${NC}"
exit 0