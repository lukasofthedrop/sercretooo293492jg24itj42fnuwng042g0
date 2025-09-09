#!/bin/bash

# Quick GitHub Setup - Método Alternativo
echo "🚀 Setup Rápido GitHub - LucrativaBet"
echo ""
read -p "Digite seu username do GitHub: " USER
echo ""
echo "📝 Criando repositório..."
echo ""
echo "1. Acesse: https://github.com/new"
echo "2. Nome: lucrativabet-dashboard"
echo "3. Privado: SIM"
echo "4. Clique em Create Repository"
echo ""
read -p "Pressione ENTER após criar o repositório..."
echo ""
echo "🔗 Configurando..."
git remote add origin https://github.com/${USER}/lucrativabet-dashboard.git
echo ""
echo "📤 Fazendo push..."
git push -u origin main
echo ""
echo "✅ Link final: https://github.com/${USER}/lucrativabet-dashboard"