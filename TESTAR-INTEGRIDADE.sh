#!/bin/bash

# SCRIPT DE TESTE DE INTEGRIDADE ANTES DE TRANSFERIR
# Execute isto para garantir que tudo está OK

echo "================================================"
echo "🔍 TESTE DE INTEGRIDADE LUCRATIVABET"
echo "================================================"
echo ""

# Cores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

ERRORS=0
WARNINGS=0

# Função para verificar arquivo
check_file() {
    if [ -f "$1" ]; then
        SIZE=$(du -k "$1" | cut -f1)
        if [ $SIZE -ge $2 ]; then
            echo -e "${GREEN}✓${NC} $1 (${SIZE}KB)"
            return 0
        else
            echo -e "${YELLOW}⚠${NC} $1 existe mas tamanho incorreto (${SIZE}KB, esperado >$2KB)"
            WARNINGS=$((WARNINGS + 1))
            return 1
        fi
    else
        echo -e "${RED}✗${NC} $1 NÃO ENCONTRADO!"
        ERRORS=$((ERRORS + 1))
        return 1
    fi
}

# Função para verificar pasta
check_dir() {
    if [ -d "$1" ]; then
        echo -e "${GREEN}✓${NC} $1"
        return 0
    else
        echo -e "${RED}✗${NC} $1 NÃO ENCONTRADO!"
        ERRORS=$((ERRORS + 1))
        return 1
    fi
}

echo "1️⃣ VERIFICANDO ARQUIVOS CRÍTICOS DO CASSINO"
echo "---------------------------------------------"
check_file "public/build/assets/app-CRDk2_8R.js" 1500  # >1.5MB
check_file "public/build/assets/app-BiLvXd5_.css" 200  # >200KB

echo ""
echo "2️⃣ VERIFICANDO PASTA DE BACKUP"
echo "---------------------------------------------"
check_dir "bet.sorte365.fun"
check_file "bet.sorte365.fun/public/build/assets/app-CRDk2_8R.js" 1500
check_file "bet.sorte365.fun/public/build/assets/app-BiLvXd5_.css" 200

echo ""
echo "3️⃣ VERIFICANDO BANCO DE DADOS"
echo "---------------------------------------------"
check_file "lucrativa.sql" 400  # >400KB
check_file ".env" 1  # >1KB

echo ""
echo "4️⃣ VERIFICANDO SCRIPTS DE RECUPERAÇÃO"
echo "---------------------------------------------"
check_file "_scripts/fix-casino-files.sh" 1
check_file "_scripts/SETUP-AUTOMATICO.sh" 5
check_file "README.md" 2
check_file "PROTOCOLO-IA.md" 2
check_file "CRITICAL-INFO.json" 3

echo ""
echo "5️⃣ VERIFICANDO ESTRUTURA LARAVEL"
echo "---------------------------------------------"
check_dir "app"
check_dir "database"
check_dir "public"
check_dir "resources"
check_dir "routes"
check_file "composer.json" 2
check_file "package.json" 1
check_file "artisan" 1

echo ""
echo "6️⃣ VERIFICANDO DOCUMENTAÇÃO"
echo "---------------------------------------------"
check_dir "_docs"
check_dir "_scripts"

echo ""
echo "================================================"
echo "📊 RESULTADO DO TESTE"
echo "================================================"

if [ $ERRORS -eq 0 ] && [ $WARNINGS -eq 0 ]; then
    echo -e "${GREEN}✅ PERFEITO!${NC} Sistema 100% íntegro e pronto para transferir!"
    echo ""
    echo "📦 Para compactar:"
    echo "tar -czf lucrativabet-completo.tar.gz \\"
    echo "  --exclude='node_modules' --exclude='vendor' --exclude='.git' \\"
    echo "  lucrativabet/"
elif [ $ERRORS -eq 0 ] && [ $WARNINGS -gt 0 ]; then
    echo -e "${YELLOW}⚠️ ATENÇÃO!${NC} Sistema funcional mas com $WARNINGS avisos."
    echo "Verifique os arquivos com tamanho incorreto."
else
    echo -e "${RED}❌ ERRO CRÍTICO!${NC} Sistema com $ERRORS erros!"
    echo ""
    echo "AÇÕES NECESSÁRIAS:"
    if [ ! -d "bet.sorte365.fun" ]; then
        echo "1. Copie a pasta bet.sorte365.fun/ (753MB) - SEM ELA NÃO FUNCIONA!"
    fi
    if [ ! -f "public/build/assets/app-CRDk2_8R.js" ]; then
        echo "2. Execute: bash _scripts/fix-casino-files.sh"
    fi
    if [ ! -f "lucrativa.sql" ]; then
        echo "3. Exporte o banco: mysqldump -u root -p lucrativabet > lucrativa.sql"
    fi
fi

echo ""
echo "================================================"

exit $ERRORS