#!/bin/bash

# ========================================
#   SISTEMA DE BACKUP - LUCRATIVABET STAGING
#   Por: ULTRATHINK
#   Backup completo: código, banco, uploads
# ========================================

# Configurações
BACKUP_DIR="/var/backups/lucrativabet"
APP_DIR="/var/www/lucrativabet-staging"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_NAME="backup_staging_${TIMESTAMP}"
MYSQL_USER="root"
MYSQL_PASS=""
DB_NAME="lucrativabet_staging"
RETENTION_DAYS=30

# Cores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}   BACKUP STAGING - LUCRATIVABET${NC}"
echo -e "${BLUE}   $(date '+%Y-%m-%d %H:%M:%S')${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""

# Criar diretório de backup se não existir
mkdir -p "$BACKUP_DIR/$BACKUP_NAME"

# 1. BACKUP DO BANCO DE DADOS
echo -e "${YELLOW}[1/5] Fazendo backup do banco de dados...${NC}"
mysqldump -u$MYSQL_USER \
    --single-transaction \
    --quick \
    --lock-tables=false \
    --routines \
    --triggers \
    --events \
    $DB_NAME > "$BACKUP_DIR/$BACKUP_NAME/database.sql"

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Backup do banco concluído${NC}"
    
    # Comprimir dump SQL
    gzip "$BACKUP_DIR/$BACKUP_NAME/database.sql"
    echo -e "${GREEN}✅ Banco comprimido${NC}"
else
    echo -e "${RED}❌ Erro no backup do banco${NC}"
    exit 1
fi

# 2. BACKUP DOS ARQUIVOS DE UPLOAD
echo -e "${YELLOW}[2/5] Fazendo backup dos uploads...${NC}"
if [ -d "$APP_DIR/storage/app/public" ]; then
    tar -czf "$BACKUP_DIR/$BACKUP_NAME/uploads.tar.gz" \
        -C "$APP_DIR/storage/app" public/ 2>/dev/null
    echo -e "${GREEN}✅ Backup dos uploads concluído${NC}"
else
    echo -e "${YELLOW}⚠️ Diretório de uploads não encontrado${NC}"
fi

# 3. BACKUP DO CÓDIGO (sem vendor e node_modules)
echo -e "${YELLOW}[3/5] Fazendo backup do código...${NC}"
tar -czf "$BACKUP_DIR/$BACKUP_NAME/code.tar.gz" \
    --exclude="$APP_DIR/vendor" \
    --exclude="$APP_DIR/node_modules" \
    --exclude="$APP_DIR/storage/logs/*" \
    --exclude="$APP_DIR/storage/framework/cache/*" \
    --exclude="$APP_DIR/storage/framework/sessions/*" \
    --exclude="$APP_DIR/storage/framework/views/*" \
    --exclude="$APP_DIR/.git" \
    -C "$(dirname $APP_DIR)" \
    "$(basename $APP_DIR)" 2>/dev/null

echo -e "${GREEN}✅ Backup do código concluído${NC}"

# 4. BACKUP DAS CONFIGURAÇÕES
echo -e "${YELLOW}[4/5] Fazendo backup das configurações...${NC}"
cp "$APP_DIR/.env" "$BACKUP_DIR/$BACKUP_NAME/env.backup" 2>/dev/null

# Backup das configurações do Nginx
cp /etc/nginx/sites-available/lucrativabet-staging "$BACKUP_DIR/$BACKUP_NAME/nginx.conf" 2>/dev/null

# Informações do sistema
echo "Backup criado em: $(date)" > "$BACKUP_DIR/$BACKUP_NAME/info.txt"
echo "Servidor: $(hostname)" >> "$BACKUP_DIR/$BACKUP_NAME/info.txt"
echo "IP: $(curl -s ifconfig.me)" >> "$BACKUP_DIR/$BACKUP_NAME/info.txt"
echo "PHP: $(php -v | head -n 1)" >> "$BACKUP_DIR/$BACKUP_NAME/info.txt"
echo "MySQL: $(mysql --version)" >> "$BACKUP_DIR/$BACKUP_NAME/info.txt"
echo "Laravel: $(cd $APP_DIR && php artisan --version)" >> "$BACKUP_DIR/$BACKUP_NAME/info.txt"

echo -e "${GREEN}✅ Backup das configurações concluído${NC}"

# 5. CRIAR ARQUIVO ÚNICO COMPACTADO
echo -e "${YELLOW}[5/5] Criando arquivo final...${NC}"
cd "$BACKUP_DIR"
tar -czf "${BACKUP_NAME}.tar.gz" "$BACKUP_NAME/"
rm -rf "$BACKUP_NAME"

# Calcular tamanho do backup
BACKUP_SIZE=$(du -h "${BACKUP_NAME}.tar.gz" | cut -f1)
echo -e "${GREEN}✅ Arquivo final criado: ${BACKUP_NAME}.tar.gz ($BACKUP_SIZE)${NC}"

# LIMPEZA DE BACKUPS ANTIGOS
echo -e "${YELLOW}Removendo backups com mais de $RETENTION_DAYS dias...${NC}"
find "$BACKUP_DIR" -name "backup_staging_*.tar.gz" -mtime +$RETENTION_DAYS -delete
REMAINING=$(ls -1 "$BACKUP_DIR"/backup_staging_*.tar.gz 2>/dev/null | wc -l)
echo -e "${GREEN}✅ Backups mantidos: $REMAINING${NC}"

# RELATÓRIO FINAL
echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}   BACKUP CONCLUÍDO COM SUCESSO!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "${BLUE}📦 ARQUIVO:${NC} $BACKUP_DIR/${BACKUP_NAME}.tar.gz"
echo -e "${BLUE}📏 TAMANHO:${NC} $BACKUP_SIZE"
echo -e "${BLUE}📅 DATA:${NC} $(date '+%Y-%m-%d %H:%M:%S')"
echo ""
echo -e "${YELLOW}PARA RESTAURAR:${NC}"
echo "1. Extrair: tar -xzf $BACKUP_DIR/${BACKUP_NAME}.tar.gz"
echo "2. Restaurar banco: gunzip -c database.sql.gz | mysql -u$MYSQL_USER $DB_NAME"
echo "3. Restaurar código: tar -xzf code.tar.gz -C /"
echo "4. Restaurar uploads: tar -xzf uploads.tar.gz -C $APP_DIR/storage/app/"
echo ""

# Opcional: enviar para storage remoto
# echo -e "${YELLOW}Enviando para storage remoto...${NC}"
# aws s3 cp "$BACKUP_DIR/${BACKUP_NAME}.tar.gz" s3://seu-bucket/backups/
# rsync -avz "$BACKUP_DIR/${BACKUP_NAME}.tar.gz" user@backup-server:/backups/

echo -e "${GREEN}✨ Backup finalizado!${NC}"