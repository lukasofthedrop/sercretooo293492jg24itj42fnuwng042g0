#!/bin/bash

# Railway Deploy Script
echo "🚀 Iniciando deploy no Railway..."

# Verificar login
if ! railway whoami > /dev/null 2>&1; then
    echo "❌ Não está logado no Railway!"
    echo "Execute: railway login"
    exit 1
fi

echo "✅ Railway autenticado"

# Criar projeto
echo "📦 Criando projeto..."
railway link

# Configurar variáveis
echo "🔧 Configurando variáveis..."
railway variables set APP_NAME="Lucrativa Bet Test"
railway variables set APP_ENV=production
railway variables set APP_KEY=base64:xFY3DNnclEWV2MaC0J4s4bht4zgLIq1N+HjfnLcVHlc=
railway variables set APP_DEBUG=false

# PlayFiver
railway variables set PLAYFIVER_URL=https://api.playfivers.com
railway variables set PLAYFIVER_CODE=sorte365bet
railway variables set PLAYFIVER_TOKEN=a9aa0e61-9179-466a-8d7b-e22e7b473b8a
railway variables set PLAYFIVER_SECRET=f41adb6a-e15b-46b4-ad5a-1fc49f4745df

# Backup Agent
railway variables set PLAYFIVER_BACKUP_CODE=lucrativabt
railway variables set PLAYFIVER_BACKUP_TOKEN=80609b36-a25c-4175-92c5-c9a6f1e1b06e
railway variables set PLAYFIVER_BACKUP_SECRET=08cfba85-7652-4a00-903f-7ea649620eb2

# Security
railway variables set TOKEN_DE_2FA=6d4b4e8e5dda575ae6679a153fce302831fd5001f58e21ba3587c96d00baa2826fa312b80425b90b02f3b7d5612d541d4dda6e5253be5565d011ea28a2cdfc5b
railway variables set FORCE_HTTPS=true
railway variables set TRUSTED_PROXIES="*"

# Deploy
echo "🚀 Fazendo deploy..."
railway up

# Gerar domínio
echo "🌐 Gerando domínio..."
railway domain

echo "✅ Deploy concluído!"