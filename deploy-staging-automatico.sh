#!/bin/bash

# ========================================
#   DEPLOY STAGING AUTOMÁTICO - LUCRATIVABET
#   Por: ULTRATHINK
#   Data: 11/09/2025
# ========================================

set -e  # Para se houver erro

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configurações
DOMAIN="teste.lucrativa.bet"
APP_DIR="/var/www/lucrativabet-staging"
BACKUP_DIR="/var/backups/lucrativabet"
PHP_VERSION="8.1"
MYSQL_USER="root"
MYSQL_PASS=""
DB_NAME="lucrativabet_staging"

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}   DEPLOY STAGING AUTOMÁTICO${NC}"
echo -e "${BLUE}   LucrativaBet - ULTRATHINK${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""

# Função para verificar se comando existe
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# 1. VERIFICAR REQUISITOS
echo -e "${YELLOW}[1/10] Verificando requisitos do sistema...${NC}"

if ! command_exists git; then
    echo -e "${RED}❌ Git não instalado. Instalando...${NC}"
    sudo apt-get update && sudo apt-get install -y git
fi

if ! command_exists php; then
    echo -e "${RED}❌ PHP não instalado. Instalando...${NC}"
    sudo apt-get install -y php${PHP_VERSION} php${PHP_VERSION}-fpm php${PHP_VERSION}-mysql \
        php${PHP_VERSION}-xml php${PHP_VERSION}-mbstring php${PHP_VERSION}-curl \
        php${PHP_VERSION}-zip php${PHP_VERSION}-gd php${PHP_VERSION}-bcmath
fi

if ! command_exists composer; then
    echo -e "${RED}❌ Composer não instalado. Instalando...${NC}"
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
fi

if ! command_exists node; then
    echo -e "${RED}❌ Node.js não instalado. Instalando...${NC}"
    curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
    sudo apt-get install -y nodejs
fi

if ! command_exists nginx; then
    echo -e "${RED}❌ Nginx não instalado. Instalando...${NC}"
    sudo apt-get install -y nginx
fi

if ! command_exists mysql; then
    echo -e "${RED}❌ MySQL não instalado. Instalando...${NC}"
    sudo apt-get install -y mysql-server
fi

echo -e "${GREEN}✅ Todos os requisitos verificados!${NC}"

# 2. CRIAR ESTRUTURA DE DIRETÓRIOS
echo -e "${YELLOW}[2/10] Criando estrutura de diretórios...${NC}"

sudo mkdir -p $APP_DIR
sudo mkdir -p $BACKUP_DIR
sudo chown -R $USER:www-data $APP_DIR

echo -e "${GREEN}✅ Diretórios criados!${NC}"

# 3. CLONAR REPOSITÓRIO OU COPIAR ARQUIVOS
echo -e "${YELLOW}[3/10] Preparando código fonte...${NC}"

if [ -d ".git" ]; then
    # Se estamos em um repositório git
    echo "Detectado repositório Git. Clonando..."
    git clone . $APP_DIR
else
    # Copiar arquivos locais
    echo "Copiando arquivos locais..."
    sudo cp -r . $APP_DIR/
fi

cd $APP_DIR

echo -e "${GREEN}✅ Código fonte preparado!${NC}"

# 4. INSTALAR DEPENDÊNCIAS
echo -e "${YELLOW}[4/10] Instalando dependências...${NC}"

composer install --no-dev --optimize-autoloader
npm install
npm run build

echo -e "${GREEN}✅ Dependências instaladas!${NC}"

# 5. CONFIGURAR AMBIENTE
echo -e "${YELLOW}[5/10] Configurando ambiente...${NC}"

# Copiar .env.staging se existir, senão criar do example
if [ -f ".env.staging" ]; then
    cp .env.staging .env
else
    cp .env.example .env
fi

# Gerar chave da aplicação
php artisan key:generate

echo -e "${GREEN}✅ Ambiente configurado!${NC}"

# 6. CONFIGURAR BANCO DE DADOS
echo -e "${YELLOW}[6/10] Configurando banco de dados...${NC}"

# Criar banco se não existir
mysql -u$MYSQL_USER -p$MYSQL_PASS -e "CREATE DATABASE IF NOT EXISTS $DB_NAME CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Rodar migrations
php artisan migrate --force

echo -e "${GREEN}✅ Banco de dados configurado!${NC}"

# 7. OTIMIZAR APLICAÇÃO
echo -e "${YELLOW}[7/10] Otimizando aplicação...${NC}"

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo -e "${GREEN}✅ Aplicação otimizada!${NC}"

# 8. CONFIGURAR PERMISSÕES
echo -e "${YELLOW}[8/10] Configurando permissões...${NC}"

sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

echo -e "${GREEN}✅ Permissões configuradas!${NC}"

# 9. CONFIGURAR NGINX
echo -e "${YELLOW}[9/10] Configurando Nginx...${NC}"

# Criar arquivo de configuração Nginx
sudo tee /etc/nginx/sites-available/lucrativabet-staging > /dev/null <<EOF
server {
    listen 80;
    server_name $DOMAIN;
    root $APP_DIR/public;

    index index.php index.html;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php${PHP_VERSION}-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Headers de segurança
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
}
EOF

# Ativar site
sudo ln -sf /etc/nginx/sites-available/lucrativabet-staging /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx

echo -e "${GREEN}✅ Nginx configurado!${NC}"

# 10. INSTALAR SSL (OPCIONAL)
echo -e "${YELLOW}[10/10] Configurando SSL...${NC}"

if command_exists certbot; then
    echo "Deseja instalar certificado SSL? (s/n)"
    read -r install_ssl
    
    if [ "$install_ssl" = "s" ]; then
        sudo certbot --nginx -d $DOMAIN --non-interactive --agree-tos --email admin@$DOMAIN
        echo -e "${GREEN}✅ SSL configurado!${NC}"
    else
        echo -e "${YELLOW}⚠️ SSL não configurado. Você pode fazer isso depois com:${NC}"
        echo "sudo certbot --nginx -d $DOMAIN"
    fi
else
    echo -e "${YELLOW}⚠️ Certbot não instalado. Instalando...${NC}"
    sudo apt-get install -y certbot python3-certbot-nginx
    echo "Execute depois: sudo certbot --nginx -d $DOMAIN"
fi

# FINALIZAÇÃO
echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}   DEPLOY CONCLUÍDO COM SUCESSO!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "${BLUE}📌 INFORMAÇÕES IMPORTANTES:${NC}"
echo -e "URL Staging: ${GREEN}http://$DOMAIN${NC}"
echo -e "Diretório: ${GREEN}$APP_DIR${NC}"
echo -e "Banco: ${GREEN}$DB_NAME${NC}"
echo ""
echo -e "${YELLOW}⚠️ PRÓXIMOS PASSOS:${NC}"
echo "1. Adicionar IP do servidor no PlayFiver"
echo "2. Configurar webhooks"
echo "3. Testar login admin"
echo ""
echo -e "${BLUE}COMANDOS ÚTEIS:${NC}"
echo "Logs: tail -f $APP_DIR/storage/logs/laravel.log"
echo "Console: cd $APP_DIR && php artisan tinker"
echo "Cache: cd $APP_DIR && php artisan cache:clear"
echo ""
echo -e "${GREEN}✨ Sistema pronto para testes!${NC}"