#!/bin/bash

# SCRIPT DE RESET RÁPIDO - LUCRATIVABET

echo "╔══════════════════════════════════════════════╗"
echo "║     RESET DO SISTEMA LUCRATIVABET           ║"
echo "╚══════════════════════════════════════════════╝"
echo ""

# Verificar estado atual
echo "📊 Verificando estado atual..."
php check-system.php

echo ""
echo "⚠️  ATENÇÃO: Este comando irá:"
echo "   • Fazer backup completo dos dados atuais"
echo "   • Remover TODOS os dados de teste"
echo "   • Manter apenas usuários administrativos"
echo "   • Zerar todas as carteiras"
echo "   • Limpar todo o cache"
echo ""
read -p "Tem certeza que deseja continuar? (sim/não): " confirm

if [ "$confirm" != "sim" ]; then
    echo "❌ Operação cancelada."
    exit 1
fi

# Executar reset
php reset-system.php

echo ""
echo "✅ Reset concluído! Sistema pronto para operação real."