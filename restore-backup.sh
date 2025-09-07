#!/bin/bash

# SCRIPT DE RESTAURAÇÃO DE BACKUP - LUCRATIVABET

echo "╔══════════════════════════════════════════════╗"
echo "║    RESTAURAÇÃO DE BACKUP LUCRATIVABET       ║"
echo "╚══════════════════════════════════════════════╝"
echo ""

# Listar backups disponíveis
echo "📁 Backups disponíveis:"
echo ""

BACKUP_DIR="storage/backups"

if [ ! -d "$BACKUP_DIR" ]; then
    echo "❌ Nenhum backup encontrado."
    exit 1
fi

# Listar diretórios de backup
ls -1 $BACKUP_DIR | grep "reset_" | nl -w2 -s". "

echo ""
read -p "Digite o número do backup que deseja restaurar (ou 'sair' para cancelar): " choice

if [ "$choice" == "sair" ]; then
    echo "❌ Operação cancelada."
    exit 0
fi

# Obter o nome do backup selecionado
BACKUP_NAME=$(ls -1 $BACKUP_DIR | grep "reset_" | sed -n "${choice}p")

if [ -z "$BACKUP_NAME" ]; then
    echo "❌ Opção inválida."
    exit 1
fi

BACKUP_PATH="$BACKUP_DIR/$BACKUP_NAME"

echo ""
echo "📂 Backup selecionado: $BACKUP_NAME"
echo ""
echo "⚠️  ATENÇÃO: Isso irá:"
echo "   • Remover todos os dados atuais"
echo "   • Restaurar os dados do backup selecionado"
echo ""
read -p "Tem certeza que deseja continuar? (sim/não): " confirm

if [ "$confirm" != "sim" ]; then
    echo "❌ Operação cancelada."
    exit 1
fi

# Executar restauração
php reset-system.php --restore "$BACKUP_PATH"

echo ""
echo "✅ Backup restaurado com sucesso!"

# Verificar estado após restauração
echo ""
echo "📊 Estado após restauração:"
php check-system.php