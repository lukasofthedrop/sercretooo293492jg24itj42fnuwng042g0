#!/bin/bash

# ============================================
# SCRIPT DE DEPLOY STAGING - LUCRATIVABET
# CIRURGIÃO DEV - PRECISÃO MÁXIMA
# Destino: teste.lucrativa.bet
# ============================================

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Configurações do servidor
SERVER_IP="179.191.222.39"
SERVER_USER="" # Será solicitado
SERVER_PATH="/var/www/teste.lucrativa.bet"
DOMAIN="teste.lucrativa.bet"
DB_NAME="lucrativabet_teste"
DB_USER="lucrativa_user"

# Arquivos locais
BACKUP_DIR="/Users/rkripto/Downloads/lucrativabet-backups"
LATEST_BACKUP=$(ls -t ${BACKUP_DIR}/lucrativabet_backup_*.tar.gz 2>/dev/null | head -1)
DB_BACKUP=$(ls -t ${BACKUP_DIR}/database_*.sql 2>/dev/null | head -1)

echo -e "${GREEN}╔════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║         DEPLOY STAGING - TESTE.LUCRATIVA.BET                  ║${NC}"
echo -e "${GREEN}║              CIRURGIÃO DEV - PRECISÃO MÁXIMA                  ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════════════════════════════╝${NC}"
echo ""

# Verificar se existe backup
if [ -z "$LATEST_BACKUP" ]; then
    echo -e "${RED}❌ Nenhum backup encontrado!${NC}"
    echo -e "${YELLOW}Execute primeiro: ./backup-projeto.sh${NC}"
    exit 1
fi

echo -e "${GREEN}📦 Backup encontrado:${NC}"
echo "   $(basename $LATEST_BACKUP)"
echo "   $(du -sh $LATEST_BACKUP | cut -f1)"
echo ""

# Solicitar informações
echo -e "${YELLOW}📝 INFORMAÇÕES NECESSÁRIAS:${NC}"
echo ""

if [ -z "$SERVER_USER" ]; then
    read -p "👤 Usuário SSH para ${SERVER_IP}: " SERVER_USER
fi

echo ""
echo -e "${BLUE}🔐 Senha do banco de dados (deixe vazio para gerar):${NC}"
read -s -p "   Senha: " DB_PASSWORD
echo ""

if [ -z "$DB_PASSWORD" ]; then
    DB_PASSWORD=$(openssl rand -base64 16)
    echo -e "${GREEN}✅ Senha gerada: ${DB_PASSWORD}${NC}"
    echo -e "${YELLOW}⚠️ GUARDE ESTA SENHA!${NC}"
fi

echo ""
echo -e "${GREEN}📋 RESUMO DO DEPLOY:${NC}"
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo "   Servidor: ${SERVER_USER}@${SERVER_IP}"
echo "   Domínio: ${DOMAIN}"
echo "   Diretório: ${SERVER_PATH}"
echo "   Banco: ${DB_NAME}"
echo "   Usuário BD: ${DB_USER}"
echo -e "${GREEN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""

read -p "Continuar com o deploy? (s/n): " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Ss]$ ]]; then
    echo -e "${RED}Deploy cancelado.${NC}"
    exit 1
fi

# ====================
# ETAPA 1: UPLOAD
# ====================
echo ""
echo -e "${YELLOW}📤 ETAPA 1: Upload dos arquivos...${NC}"
echo "Fazendo upload do backup..."
scp "$LATEST_BACKUP" "${SERVER_USER}@${SERVER_IP}:/tmp/"

if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Erro no upload!${NC}"
    exit 1
fi

echo "Fazendo upload do banco de dados..."
scp "$DB_BACKUP" "${SERVER_USER}@${SERVER_IP}:/tmp/"

echo -e "${GREEN}✅ Upload concluído${NC}"

# ====================
# ETAPA 2: SCRIPT REMOTO
# ====================
echo ""
echo -e "${YELLOW}🖥️ ETAPA 2: Configurando servidor...${NC}"

# Criar script de instalação remoto
cat > /tmp/install_staging.sh << 'REMOTE_SCRIPT'
#!/bin/bash

# Variáveis passadas
SERVER_PATH="__SERVER_PATH__"
DOMAIN="__DOMAIN__"
DB_NAME="__DB_NAME__"
DB_USER="__DB_USER__"
DB_PASSWORD="__DB_PASSWORD__"
BACKUP_FILE="__BACKUP_FILE__"
DB_FILE="__DB_FILE__"

echo "🔧 Instalando dependências..."
apt-get update
apt-get install -y nginx mysql-server redis-server \
    php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml \
    php8.2-bcmath php8.2-curl php8.2-zip php8.2-gd \
    php8.2-redis composer git certbot python3-certbot-nginx

echo "📁 Criando estrutura..."
mkdir -p ${SERVER_PATH}
cd ${SERVER_PATH}

echo "📦 Extraindo projeto..."
tar -xzf /tmp/$(basename ${BACKUP_FILE})

echo "🔐 Configurando .env..."
cp .env.production .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=${DB_PASSWORD}/" .env
sed -i "s/TOKEN_DE_2FA=.*/TOKEN_DE_2FA=$(openssl rand -hex 16)/" .env
sed -i "s/JWT_SECRET=.*/JWT_SECRET=$(openssl rand -base64 32)/" .env

echo "💾 Configurando banco de dados..."
mysql -u root << SQL
CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
SQL

echo "📊 Importando dados..."
mysql -u ${DB_USER} -p${DB_PASSWORD} ${DB_NAME} < /tmp/$(basename ${DB_FILE})

echo "📦 Instalando dependências PHP..."
cd ${SERVER_PATH}
composer install --optimize-autoloader --no-dev

echo "🔑 Gerando chave da aplicação..."
php artisan key:generate

echo "🔄 Executando migrations..."
php artisan migrate --force

echo "🔐 Configurando permissões..."
chown -R www-data:www-data ${SERVER_PATH}
chmod -R 755 ${SERVER_PATH}/storage
chmod -R 755 ${SERVER_PATH}/bootstrap/cache

echo "🌐 Configurando Nginx..."
cat > /etc/nginx/sites-available/${DOMAIN} << NGINX_CONF
server {
    listen 80;
    server_name ${DOMAIN};
    root ${SERVER_PATH}/public;
    
    index index.php index.html;
    
    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
NGINX_CONF

ln -sf /etc/nginx/sites-available/${DOMAIN} /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx

echo "⚡ Otimizando aplicação..."
cd ${SERVER_PATH}
php artisan config:cache
php artisan route:cache
php artisan view:clear
php artisan optimize

echo "🔄 Reiniciando serviços..."
systemctl restart php8.2-fpm
systemctl restart nginx
systemctl restart redis-server

echo "✅ Instalação concluída!"
REMOTE_SCRIPT

# Substituir variáveis no script
sed -i.bak "s|__SERVER_PATH__|${SERVER_PATH}|g" /tmp/install_staging.sh
sed -i.bak "s|__DOMAIN__|${DOMAIN}|g" /tmp/install_staging.sh
sed -i.bak "s|__DB_NAME__|${DB_NAME}|g" /tmp/install_staging.sh
sed -i.bak "s|__DB_USER__|${DB_USER}|g" /tmp/install_staging.sh
sed -i.bak "s|__DB_PASSWORD__|${DB_PASSWORD}|g" /tmp/install_staging.sh
sed -i.bak "s|__BACKUP_FILE__|${LATEST_BACKUP}|g" /tmp/install_staging.sh
sed -i.bak "s|__DB_FILE__|${DB_BACKUP}|g" /tmp/install_staging.sh

# Upload e execução do script
echo "Enviando script de instalação..."
scp /tmp/install_staging.sh "${SERVER_USER}@${SERVER_IP}:/tmp/"

echo ""
echo -e "${YELLOW}🚀 Executando instalação no servidor...${NC}"
echo -e "${YELLOW}(Isso pode demorar alguns minutos)${NC}"
echo ""

ssh "${SERVER_USER}@${SERVER_IP}" "chmod +x /tmp/install_staging.sh && sudo /tmp/install_staging.sh"

if [ $? -eq 0 ]; then
    echo ""
    echo -e "${GREEN}╔════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║              DEPLOY CONCLUÍDO COM SUCESSO!                    ║${NC}"
    echo -e "${GREEN}╚════════════════════════════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "${GREEN}🌐 Acesse: http://${DOMAIN}${NC}"
    echo ""
    echo -e "${YELLOW}📋 PRÓXIMOS PASSOS:${NC}"
    echo "1. Configurar DNS: ${DOMAIN} → ${SERVER_IP}"
    echo "2. Aguardar propagação DNS (5-30 min)"
    echo "3. Instalar SSL: sudo certbot --nginx -d ${DOMAIN}"
    echo "4. Adicionar IP na PlayFivers: ${SERVER_IP}"
    echo "5. Testar aplicação completa"
    echo ""
    echo -e "${BLUE}📊 INFORMAÇÕES DO BANCO:${NC}"
    echo "   Database: ${DB_NAME}"
    echo "   Usuário: ${DB_USER}"
    echo "   Senha: ${DB_PASSWORD}"
    echo ""
    echo -e "${YELLOW}⚠️ GUARDE ESTAS INFORMAÇÕES!${NC}"
else
    echo -e "${RED}❌ Erro durante a instalação!${NC}"
    echo "Verifique os logs no servidor."
fi

# Limpar arquivos temporários
rm -f /tmp/install_staging.sh /tmp/install_staging.sh.bak