#!/bin/bash

# ==================================================
# 🧪 LUCRATIVABET - TESTE COMPLETO AUTOMATIZADO
# ==================================================

# Cores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

# Contadores
TESTS_PASSED=0
TESTS_FAILED=0

# Função de teste
run_test() {
    local test_name=$1
    local test_command=$2
    local expected=$3
    
    echo -n "Testing: $test_name... "
    
    result=$(eval $test_command 2>/dev/null)
    
    if [[ "$result" == *"$expected"* ]]; then
        echo -e "${GREEN}✅ PASSED${NC}"
        ((TESTS_PASSED++))
        return 0
    else
        echo -e "${RED}❌ FAILED${NC}"
        echo "  Expected: $expected"
        echo "  Got: $result"
        ((TESTS_FAILED++))
        return 1
    fi
}

echo "=================================================="
echo "🧪 TESTE COMPLETO DO SISTEMA LUCRATIVABET"
echo "=================================================="
echo "$(date '+%Y-%m-%d %H:%M:%S')"
echo "=================================================="
echo

# 1. TESTES DE INFRAESTRUTURA
echo "1️⃣ INFRAESTRUTURA"
echo "---------------------------------------------"

run_test "PHP Version" "php -v | head -n1" "PHP 8"
run_test "Composer" "ls vendor/autoload.php 2>&1" "vendor/autoload.php"
run_test "NPM Packages" "ls -d node_modules 2>&1" "node_modules"
run_test "Redis Running" "redis-cli ping" "PONG"
run_test "Laravel Artisan" "php artisan --version" "Laravel Framework"

echo

# 2. TESTES DE SERVIDOR
echo "2️⃣ SERVIDOR WEB"
echo "---------------------------------------------"

run_test "Server Running" "curl -s -o /dev/null -w '%{http_code}' http://localhost:8000" "200"
run_test "Homepage Title" "curl -s http://localhost:8000 | grep -o '<title>.*</title>'" "Cassino"
run_test "Admin Route" "curl -s -o /dev/null -w '%{http_code}' http://localhost:8000/admin/login" "200"
run_test "API Health" "curl -s http://localhost:8000/api" "API is running"
run_test "Games API" "curl -s -o /dev/null -w '%{http_code}' http://localhost:8000/api/games" "200"

echo

# 3. TESTES DE ASSETS
echo "3️⃣ ASSETS COMPILADOS"
echo "---------------------------------------------"

run_test "CSS File" "curl -s -o /dev/null -w '%{http_code}' http://localhost:8000/build/assets/app-BiLvXd5_.css" "200"
run_test "JS File" "curl -s -o /dev/null -w '%{http_code}' http://localhost:8000/build/assets/app-CRDk2_8R.js" "200"
run_test "Manifest" "ls public/build/manifest.json 2>&1" "manifest.json"
run_test "Images Dir" "ls -d public/images 2>&1" "public/images"

echo

# 4. TESTES DE CONFIGURAÇÃO
echo "4️⃣ CONFIGURAÇÃO"
echo "---------------------------------------------"

run_test "ENV File" "ls .env 2>&1" ".env"
run_test "Cache Driver" "grep CACHE_DRIVER .env" "redis"
run_test "Session Driver" "grep SESSION_DRIVER .env" "redis"
run_test "Queue Driver" "grep QUEUE_CONNECTION .env" "redis"
run_test "Redis Client" "grep REDIS_CLIENT .env" "predis"

echo

# 5. TESTES DE WORKERS
echo "5️⃣ QUEUE WORKERS"
echo "---------------------------------------------"

WORKERS=$(ps aux | grep -c "[a]rtisan queue:work")
if [ $WORKERS -gt 0 ]; then
    echo -e "${GREEN}✅ PASSED${NC} - $WORKERS workers rodando"
    ((TESTS_PASSED++))
else
    echo -e "${RED}❌ FAILED${NC} - Nenhum worker rodando"
    ((TESTS_FAILED++))
fi

echo

# 6. TESTES DE DIRETÓRIOS
echo "6️⃣ ESTRUTURA DE DIRETÓRIOS"
echo "---------------------------------------------"

run_test "App Directory" "ls -d app 2>&1" "app"
run_test "Database Dir" "ls -d database 2>&1" "database"
run_test "Public Dir" "ls -d public 2>&1" "public"
run_test "Resources Dir" "ls -d resources 2>&1" "resources"
run_test "Routes Dir" "ls -d routes 2>&1" "routes"
run_test "Storage Writable" "touch storage/test.tmp && rm storage/test.tmp && echo 'writable'" "writable"

echo

# 7. TESTES DE SEGURANÇA
echo "7️⃣ SEGURANÇA"
echo "---------------------------------------------"

run_test "ENV not accessible" "curl -s -o /dev/null -w '%{http_code}' http://localhost:8000/.env" "404"
run_test "Git not exposed" "curl -s -o /dev/null -w '%{http_code}' http://localhost:8000/.git/config" "404"
run_test "Debug Mode" "grep APP_DEBUG .env" "false"

echo

# 8. TESTES UNITÁRIOS
echo "8️⃣ TESTES UNITÁRIOS"
echo "---------------------------------------------"

if php artisan test tests/Feature/BasicSystemTest.php --compact 2>&1 | grep -q "PASS"; then
    echo -e "${GREEN}✅ PASSED${NC} - Todos os testes unitários passaram"
    ((TESTS_PASSED++))
else
    echo -e "${RED}❌ FAILED${NC} - Alguns testes unitários falharam"
    ((TESTS_FAILED++))
fi

echo

# 9. PERFORMANCE
echo "9️⃣ PERFORMANCE"
echo "---------------------------------------------"

RESPONSE_TIME=$(curl -o /dev/null -s -w '%{time_total}' http://localhost:8000)
if (( $(echo "$RESPONSE_TIME < 1" | bc -l) )); then
    echo -e "${GREEN}✅ PASSED${NC} - Homepage response: ${RESPONSE_TIME}s"
    ((TESTS_PASSED++))
else
    echo -e "${YELLOW}⚠️  WARNING${NC} - Homepage response: ${RESPONSE_TIME}s (lento)"
fi

echo

# RESULTADO FINAL
echo "=================================================="
echo "📊 RESULTADO FINAL"
echo "=================================================="

TOTAL_TESTS=$((TESTS_PASSED + TESTS_FAILED))
PERCENTAGE=$((TESTS_PASSED * 100 / TOTAL_TESTS))

echo "Total de testes: $TOTAL_TESTS"
echo -e "Passou: ${GREEN}$TESTS_PASSED${NC}"
echo -e "Falhou: ${RED}$TESTS_FAILED${NC}"
echo "Taxa de sucesso: ${PERCENTAGE}%"

echo

if [ $TESTS_FAILED -eq 0 ]; then
    echo -e "${GREEN}🎉 SISTEMA 100% FUNCIONAL!${NC}"
    echo "=================================================="
    exit 0
else
    echo -e "${YELLOW}⚠️  SISTEMA COM $TESTS_FAILED FALHAS${NC}"
    echo "=================================================="
    exit 1
fi