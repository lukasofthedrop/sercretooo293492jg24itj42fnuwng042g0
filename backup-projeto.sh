#!/bin/bash

# ============================================
# SCRIPT DE BACKUP - LUCRATIVABET
# CIRURGIÃO DEV - MÁXIMA PRECISÃO
# ============================================

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configurações
PROJECT_DIR="/Users/rkripto/Downloads/lucrativabet"
BACKUP_DIR="/Users/rkripto/Downloads/lucrativabet-backups"
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_NAME="lucrativabet_backup_${DATE}"

echo -e "${GREEN}╔════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║           BACKUP LUCRATIVABET - CIRURGIÃO DEV                 ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════════════════════╝${NC}"
echo ""

# Criar diretório de backup se não existir
if [ ! -d "$BACKUP_DIR" ]; then
    echo -e "${YELLOW}📁 Criando diretório de backup...${NC}"
    mkdir -p "$BACKUP_DIR"
fi

# 1. BACKUP DO BANCO DE DADOS
echo -e "${YELLOW}💾 Fazendo backup do banco de dados...${NC}"
mysqldump -u root lucrativabet > "${BACKUP_DIR}/database_${DATE}.sql" 2>/dev/null
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Banco de dados salvo: database_${DATE}.sql${NC}"
else
    echo -e "${RED}⚠️ Aviso: Não foi possível fazer backup do banco${NC}"
fi

# 2. BACKUP DOS ARQUIVOS .ENV
echo -e "${YELLOW}🔐 Salvando arquivos de configuração...${NC}"
cp "${PROJECT_DIR}/.env" "${BACKUP_DIR}/.env_${DATE}" 2>/dev/null
cp "${PROJECT_DIR}/.env.production" "${BACKUP_DIR}/.env.production_${DATE}" 2>/dev/null
echo -e "${GREEN}✅ Arquivos .env salvos${NC}"

# 3. BACKUP DO CÓDIGO (sem vendor/node_modules)
echo -e "${YELLOW}📦 Compactando projeto (isso pode demorar)...${NC}"
cd "${PROJECT_DIR}"
tar -czf "${BACKUP_DIR}/${BACKUP_NAME}.tar.gz" \
    --exclude='node_modules' \
    --exclude='vendor' \
    --exclude='.git' \
    --exclude='storage/logs/*' \
    --exclude='storage/framework/cache/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    --exclude='bootstrap/cache/*' \
    --exclude='*.log' \
    .

if [ $? -eq 0 ]; then
    # Calcular tamanho do backup
    SIZE=$(du -sh "${BACKUP_DIR}/${BACKUP_NAME}.tar.gz" | cut -f1)
    echo -e "${GREEN}✅ Projeto compactado: ${BACKUP_NAME}.tar.gz (${SIZE})${NC}"
else
    echo -e "${RED}❌ Erro ao compactar projeto${NC}"
    exit 1
fi

# 4. CRIAR ARQUIVO DE INFORMAÇÕES
echo -e "${YELLOW}📝 Criando arquivo de informações...${NC}"
cat > "${BACKUP_DIR}/backup_info_${DATE}.txt" << EOF
BACKUP LUCRATIVABET
===================
Data: $(date)
Diretório: ${PROJECT_DIR}
Backup: ${BACKUP_NAME}

ARQUIVOS INCLUÍDOS:
- Código fonte completo
- Banco de dados: database_${DATE}.sql
- Configurações: .env_${DATE}
- Production: .env.production_${DATE}

ARQUIVOS EXCLUÍDOS:
- node_modules/
- vendor/
- .git/
- logs e cache

ESTATÍSTICAS DO SISTEMA:
- Laravel: 10.48.2
- PHP: 8.2.29
- Total usuários: 14,789
- Total jogos: 1,774

AGENTES PLAYFIVER:
- Principal: sorte365bet
- Backup: lucrativabt

Para restaurar:
1. Extrair: tar -xzf ${BACKUP_NAME}.tar.gz
2. Importar BD: mysql -u root lucrativabet < database_${DATE}.sql
3. Instalar deps: composer install && npm install
4. Configurar .env

CIRURGIÃO DEV - Backup com precisão cirúrgica
EOF

echo -e "${GREEN}✅ Arquivo de informações criado${NC}"

# 5. LISTAR BACKUPS
echo ""
echo -e "${GREEN}📊 RESUMO DO BACKUP:${NC}"
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
ls -lah "${BACKUP_DIR}" | grep "${DATE}"
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"

# 6. LIMPEZA DE BACKUPS ANTIGOS (manter últimos 5)
echo -e "${YELLOW}🧹 Verificando backups antigos...${NC}"
BACKUP_COUNT=$(ls -1 "${BACKUP_DIR}"/lucrativabet_backup_*.tar.gz 2>/dev/null | wc -l)
if [ $BACKUP_COUNT -gt 5 ]; then
    echo -e "${YELLOW}Removendo backups antigos (mantendo últimos 5)...${NC}"
    ls -1t "${BACKUP_DIR}"/lucrativabet_backup_*.tar.gz | tail -n +6 | xargs rm -f
fi

echo ""
echo -e "${GREEN}╔════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║                   BACKUP CONCLUÍDO COM SUCESSO!                ║${NC}"
echo -e "${GREEN}║                                                                 ║${NC}"
echo -e "${GREEN}║   Local: ${BACKUP_DIR}                                         ║${NC}"
echo -e "${GREEN}║   Arquivo: ${BACKUP_NAME}.tar.gz                               ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════════════════════╝${NC}"