#!/bin/bash

echo "🚀 AUTOMAÇÃO DE DEPLOY - LUCRATIVABET NO RENDER"
echo "=================================================="

# Verificar dependências
echo "📦 Verificando dependências..."
if ! command -v node &> /dev/null; then
    echo "❌ Node.js não encontrado!"
    exit 1
fi

if ! command -v npm &> /dev/null; then
    echo "❌ NPM não encontrado!"
    exit 1
fi

# Verificar Playwright
echo "🎭 Verificando Playwright..."
if ! npm list playwright &> /dev/null; then
    echo "⬇️ Instalando Playwright..."
    npm install playwright
fi

# Verificar navegadores Playwright
echo "🌐 Verificando navegadores Playwright..."
if ! npx playwright --version &> /dev/null; then
    echo "⬇️ Instalando navegadores Playwright..."
    npx playwright install
fi

echo "✅ Dependências verificadas!"
echo ""

# Perguntar sobre a senha do GitHub
echo "🔐 Configuração de autenticação"
echo "=============================="
read -p "Digite sua senha do GitHub (rk.impulsodigital@gmail.com): " -s GITHUB_PASSWORD
echo ""
echo ""

# Exportar variável de ambiente
export GITHUB_PASSWORD="$GITHUB_PASSWORD"

# Perguntar sobre modo headless
read -p "Executar em modo headless (s/N)? " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Ss]$ ]]; then
    echo "🤖 Executando em modo headless..."
    # Modificar o script para modo headless
    sed -i '' 's/headless: false/headless: true/' render-deploy-automation.js
    sed -i '' 's/slowMo: 1000/slowMo: 500/' render-deploy-automation.js
else
    echo "👁️ Executando em modo visível..."
    # Modificar o script para modo visível
    sed -i '' 's/headless: true/headless: false/' render-deploy-automation.js
    sed -i '' 's/slowMo: 500/slowMo: 1000/' render-deploy-automation.js
fi

echo ""
echo "🚀 INICIANDO AUTOMAÇÃO..."
echo "========================="
echo "📋 O script irá:"
echo "   1. Abrir navegador e acessar dashboard.render.com"
echo "   2. Fazer login com GitHub"
echo "   3. Criar novo Web Service"
echo "   4. Conectar ao repositório lukasofthedrop/sercretooo293492jg24itj42fnuwng042g0.git"
echo "   5. Selecionar branch render-clean"
echo "   6. Aguardar detecção do render.yaml"
echo "   7. Iniciar o deploy"
echo "   8. Monitorar até ficar online"
echo ""
echo "⚠️ IMPORTANTE: Não feche o terminal durante a execução!"
echo ""
read -p "Pressione ENTER para começar..."

# Executar a automação
echo "🎬 Iniciando script de automação..."
node render-deploy-automation.js

echo ""
echo "✅ AUTOMAÇÃO CONCLUÍDA!"
echo "======================="
echo "📸 Screenshots salvos em:"
echo "   - deploy-final-status.png (sucesso)"
echo "   - deploy-error.png (se houve erro)"
echo ""
echo "🔍 Verifique o dashboard do Render para confirmar o status final"
echo "🌐 URL do serviço: https://dashboard.render.com"