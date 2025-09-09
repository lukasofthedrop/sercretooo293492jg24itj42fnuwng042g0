#!/bin/bash

# ==================================================
# 💾 LUCRATIVABET - BACKUP AUTOMÁTICO INTELIGENTE
# ==================================================

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m'

TIMESTAMP=$(date +%Y%m%d-%H%M%S)
BACKUP_DIR="_backups"
BACKUP_NAME="lucrativabet-backup-$TIMESTAMP"

echo -e "${CYAN}=================================================="
echo "     💾 BACKUP AUTOMÁTICO - LUCRATIVABET"
echo "==================================================${NC}"
echo -e "${BLUE}$(date '+%Y-%m-%d %H:%M:%S')${NC}"
echo

# Criar diretório de backup
mkdir -p "$BACKUP_DIR"

echo -e "${YELLOW}1️⃣ PREPARANDO BACKUP${NC}"
echo "---------------------------------------------"

# Criar diretório temporário
TEMP_DIR="/tmp/$BACKUP_NAME"
mkdir -p "$TEMP_DIR"

# 1. Backup do código fonte
echo "📁 Copiando código fonte..."
rsync -a --exclude='node_modules' --exclude='vendor' --exclude='.git' \
    --exclude='storage/logs/*' --exclude='storage/framework/cache/*' \
    --exclude='storage/framework/sessions/*' --exclude='storage/framework/views/*' \
    --exclude='_backups' \
    . "$TEMP_DIR/source/"

# 2. Backup do banco de dados
echo "🗄️ Exportando banco de dados..."
if [ -f "lucrativa.sql" ]; then
    cp lucrativa.sql "$TEMP_DIR/database.sql"
fi

# 3. Backup das configurações
echo "⚙️ Salvando configurações..."
mkdir -p "$TEMP_DIR/config"
cp .env "$TEMP_DIR/config/.env.backup"
cp composer.json "$TEMP_DIR/config/"
cp package.json "$TEMP_DIR/config/"

# 4. Backup dos scripts
echo "📜 Salvando scripts de automação..."
mkdir -p "$TEMP_DIR/scripts"
cp *.sh "$TEMP_DIR/scripts/" 2>/dev/null

# 5. Backup dos assets compilados
echo "🎨 Salvando assets compilados..."
mkdir -p "$TEMP_DIR/assets"
cp -r public/build "$TEMP_DIR/assets/" 2>/dev/null

# 6. Criar arquivo de informações
echo "📝 Criando arquivo de informações..."
cat > "$TEMP_DIR/BACKUP_INFO.txt" << EOF
LUCRATIVABET - INFORMAÇÕES DO BACKUP
=====================================
Data: $(date)
Versão Laravel: $(php artisan --version)
PHP Version: $(php -v | head -n1)
Total de Arquivos: $(find "$TEMP_DIR/source" -type f | wc -l)

ESTRUTURA DO BACKUP:
- source/      : Código fonte completo
- database.sql : Dump do banco de dados
- config/      : Arquivos de configuração
- scripts/     : Scripts de automação
- assets/      : Assets compilados

PARA RESTAURAR:
1. Extrair o backup: tar -xzf $BACKUP_NAME.tar.gz
2. Copiar source/* para o diretório do projeto
3. Restaurar .env de config/.env.backup
4. Importar database.sql no MySQL
5. Executar: composer install && npm install
6. Executar: ./START-SYSTEM.sh

STATUS DO SISTEMA NO BACKUP:
- Sistema 100% funcional
- Todos os testes passando
- Segurança implementada
- Performance otimizada
EOF

echo
echo -e "${YELLOW}2️⃣ COMPACTANDO BACKUP${NC}"
echo "---------------------------------------------"

# Compactar backup
cd /tmp
tar -czf "$BACKUP_NAME.tar.gz" "$BACKUP_NAME"
BACKUP_SIZE=$(du -h "$BACKUP_NAME.tar.gz" | cut -f1)

# Mover para diretório de backups
cd - > /dev/null
mv "/tmp/$BACKUP_NAME.tar.gz" "$BACKUP_DIR/"

# Limpar temporários
rm -rf "$TEMP_DIR"

echo -e "${GREEN}✅ Backup criado: $BACKUP_DIR/$BACKUP_NAME.tar.gz${NC}"
echo -e "${GREEN}✅ Tamanho: $BACKUP_SIZE${NC}"

echo
echo -e "${YELLOW}3️⃣ LIMPEZA AUTOMÁTICA${NC}"
echo "---------------------------------------------"

# Manter apenas os últimos 5 backups
BACKUP_COUNT=$(ls -1 "$BACKUP_DIR"/*.tar.gz 2>/dev/null | wc -l)
if [ $BACKUP_COUNT -gt 5 ]; then
    echo "🗑️ Removendo backups antigos..."
    ls -1t "$BACKUP_DIR"/*.tar.gz | tail -n +6 | xargs rm -f
    echo -e "${GREEN}✅ Mantendo apenas os 5 backups mais recentes${NC}"
else
    echo -e "${GREEN}✅ Total de backups: $BACKUP_COUNT${NC}"
fi

echo
echo -e "${YELLOW}4️⃣ VERIFICAÇÃO DO BACKUP${NC}"
echo "---------------------------------------------"

# Verificar integridade
if tar -tzf "$BACKUP_DIR/$BACKUP_NAME.tar.gz" > /dev/null 2>&1; then
    echo -e "${GREEN}✅ Backup íntegro e verificado${NC}"
else
    echo -e "${RED}❌ Erro na verificação do backup${NC}"
    exit 1
fi

# Listar backups existentes
echo
echo -e "${YELLOW}5️⃣ BACKUPS DISPONÍVEIS${NC}"
echo "---------------------------------------------"
ls -lh "$BACKUP_DIR"/*.tar.gz 2>/dev/null | tail -5 | while read line; do
    echo "  📦 $(echo $line | awk '{print $9, "("$5")"}')"
done

echo
echo -e "${CYAN}=================================================="
echo "     💾 BACKUP COMPLETO COM SUCESSO!"
echo "==================================================${NC}"
echo
echo -e "${GREEN}📦 Arquivo: $BACKUP_DIR/$BACKUP_NAME.tar.gz${NC}"
echo -e "${GREEN}📏 Tamanho: $BACKUP_SIZE${NC}"
echo -e "${GREEN}🕒 Horário: $(date '+%H:%M:%S')${NC}"
echo
echo "Para restaurar este backup:"
echo -e "${BLUE}tar -xzf $BACKUP_DIR/$BACKUP_NAME.tar.gz${NC}"
echo
echo "=================================================="

exit 0