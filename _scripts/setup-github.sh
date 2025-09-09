#!/bin/bash

# Setup GitHub Repository for LucrativaBet Dashboard
# Execute este script para configurar o repositório

echo "🚀 Configuração do Repositório GitHub - LucrativaBet Dashboard"
echo "============================================================"
echo ""

# Solicitar informações do usuário
read -p "Digite seu username do GitHub: " GITHUB_USER
read -s -p "Digite seu Personal Access Token (PAT): " GITHUB_TOKEN
echo ""

# Verificar se o token foi fornecido
if [ -z "$GITHUB_TOKEN" ]; then
    echo "❌ Token não fornecido. Para criar um token:"
    echo "1. Acesse: https://github.com/settings/tokens"
    echo "2. Clique em 'Generate new token (classic)'"
    echo "3. Marque as permissões: repo, workflow"
    echo "4. Copie o token gerado"
    exit 1
fi

echo ""
echo "📝 Criando repositório no GitHub..."

# Criar o repositório usando a API do GitHub
RESPONSE=$(curl -s -H "Authorization: token $GITHUB_TOKEN" \
    -d '{"name":"lucrativabet-dashboard","description":"Dashboard administrativo da plataforma LucrativaBet - Sistema de apostas com análise VIP","private":true}' \
    https://api.github.com/user/repos)

# Verificar se houve erro
if echo "$RESPONSE" | grep -q "Bad credentials"; then
    echo "❌ Token inválido. Verifique seu Personal Access Token."
    exit 1
fi

if echo "$RESPONSE" | grep -q "already exists"; then
    echo "⚠️  Repositório já existe. Continuando com a configuração..."
else
    echo "✅ Repositório criado com sucesso!"
fi

echo ""
echo "🔗 Configurando remote origin..."

# Remover remote antigo se existir
git remote remove origin 2>/dev/null

# Adicionar novo remote
git remote add origin https://${GITHUB_USER}:${GITHUB_TOKEN}@github.com/${GITHUB_USER}/lucrativabet-dashboard.git

echo "✅ Remote configurado!"
echo ""
echo "📤 Enviando commits para o GitHub..."

# Fazer push
git push -u origin main

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ SUCESSO! Repositório configurado e código enviado!"
    echo ""
    echo "🔗 Link do repositório:"
    echo "   https://github.com/${GITHUB_USER}/lucrativabet-dashboard"
    echo ""
    echo "📊 Commits enviados:"
    git log --oneline -5
else
    echo "❌ Erro ao fazer push. Verifique suas credenciais."
fi