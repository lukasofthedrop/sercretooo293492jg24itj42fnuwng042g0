#!/bin/bash

# ========================================
#   INSTALADOR DE DEPENDÊNCIAS - SERVIDOR STAGING
#   LucrativaBet - ULTRATHINK
#   Compatível com: Ubuntu 20.04+ / Debian 10+
# ========================================

set -e

# Cores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}   INSTALAÇÃO DE DEPENDÊNCIAS${NC}"
echo -e "${BLUE}   LucrativaBet Staging${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""

# Detectar sistema operacional
if [ -f /etc/os-release ]; then
    . /etc/os-release
    OS=$NAME
    VERSION=$VERSION_ID
else
    echo -e "${RED}Sistema operacional não suportado${NC}"
    exit 1
fi

echo -e "${YELLOW}Sistema detectado: $OS $VERSION${NC}"
echo ""

# Atualizar sistema
echo -e "${YELLOW}[1/8] Atualizando sistema...${NC}"
sudo apt-get update -y
sudo apt-get upgrade -y

# Instalar ferramentas básicas
echo -e "${YELLOW}[2/8] Instalando ferramentas básicas...${NC}"
sudo apt-get install -y \
    curl \
    wget \
    git \
    zip \
    unzip \
    software-properties-common \
    apt-transport-https \
    ca-certificates \
    gnupg \
    lsb-release \
    htop \
    nano \
    vim \
    ufw

echo -e "${GREEN}✅ Ferramentas básicas instaladas${NC}"

# Instalar PHP 8.1 e extensões
echo -e "${YELLOW}[3/8] Instalando PHP 8.1...${NC}"
sudo add-apt-repository ppa:ondrej/php -y
sudo apt-get update
sudo apt-get install -y \
    php8.1 \
    php8.1-fpm \
    php8.1-cli \
    php8.1-common \
    php8.1-mysql \
    php8.1-pgsql \
    php8.1-sqlite3 \
    php8.1-xml \
    php8.1-mbstring \
    php8.1-curl \
    php8.1-zip \
    php8.1-gd \
    php8.1-bcmath \
    php8.1-intl \
    php8.1-readline \
    php8.1-msgpack \
    php8.1-igbinary \
    php8.1-redis \
    php8.1-apcu \
    php8.1-opcache

# Configurar PHP
sudo tee /etc/php/8.1/fpm/conf.d/99-lucrativabet.ini > /dev/null <<EOF
upload_max_filesize = 100M
post_max_size = 100M
max_execution_time = 300
max_input_time = 300
memory_limit = 256M
opcache.enable = 1
opcache.memory_consumption = 256
opcache.max_accelerated_files = 20000
opcache.revalidate_freq = 0
EOF

sudo systemctl restart php8.1-fpm
echo -e "${GREEN}✅ PHP 8.1 instalado e configurado${NC}"

# Instalar Composer
echo -e "${YELLOW}[4/8] Instalando Composer...${NC}"
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
composer --version
echo -e "${GREEN}✅ Composer instalado${NC}"

# Instalar Node.js 18 e NPM
echo -e "${YELLOW}[5/8] Instalando Node.js 18...${NC}"
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
node --version
npm --version
echo -e "${GREEN}✅ Node.js e NPM instalados${NC}"

# Instalar MySQL 8
echo -e "${YELLOW}[6/8] Instalando MySQL 8...${NC}"
sudo apt-get install -y mysql-server mysql-client

# Configurar MySQL
sudo mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '';"
sudo mysql -e "FLUSH PRIVILEGES;"
sudo mysql -e "CREATE DATABASE IF NOT EXISTS lucrativabet_staging CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo -e "${GREEN}✅ MySQL instalado e configurado${NC}"

# Instalar Nginx
echo -e "${YELLOW}[7/8] Instalando Nginx...${NC}"
sudo apt-get install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx
echo -e "${GREEN}✅ Nginx instalado${NC}"

# Instalar Redis (opcional mas recomendado)
echo -e "${YELLOW}[8/8] Instalando Redis...${NC}"
sudo apt-get install -y redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server
echo -e "${GREEN}✅ Redis instalado${NC}"

# Instalar Certbot para SSL
echo -e "${YELLOW}[Extra] Instalando Certbot...${NC}"
sudo apt-get install -y certbot python3-certbot-nginx
echo -e "${GREEN}✅ Certbot instalado${NC}"

# Configurar Firewall
echo -e "${YELLOW}Configurando Firewall...${NC}"
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw allow 3306/tcp  # MySQL (apenas se necessário acesso externo)
sudo ufw allow 6379/tcp  # Redis (apenas se necessário acesso externo)
# sudo ufw enable # Descomentar para ativar (cuidado com SSH!)
echo -e "${GREEN}✅ Regras de firewall configuradas (não ativado)${NC}"

# Criar diretórios necessários
echo -e "${YELLOW}Criando estrutura de diretórios...${NC}"
sudo mkdir -p /var/www
sudo mkdir -p /var/backups/lucrativabet
sudo chown -R $USER:www-data /var/www
echo -e "${GREEN}✅ Diretórios criados${NC}"

# Verificar serviços
echo ""
echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}   STATUS DOS SERVIÇOS${NC}"
echo -e "${BLUE}========================================${NC}"

# Função para verificar serviço
check_service() {
    if systemctl is-active --quiet $1; then
        echo -e "$2: ${GREEN}✅ Ativo${NC}"
    else
        echo -e "$2: ${RED}❌ Inativo${NC}"
    fi
}

check_service "php8.1-fpm" "PHP-FPM"
check_service "nginx" "Nginx"
check_service "mysql" "MySQL"
check_service "redis-server" "Redis"

# Informações finais
echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}   INSTALAÇÃO CONCLUÍDA!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "${BLUE}📌 VERSÕES INSTALADAS:${NC}"
php -v | head -n 1
composer --version
node --version
mysql --version
nginx -v
redis-server --version
echo ""
echo -e "${YELLOW}⚠️ PRÓXIMOS PASSOS:${NC}"
echo "1. Execute o script de deploy: ./deploy-staging-automatico.sh"
echo "2. Configure o arquivo .env.staging com suas credenciais"
echo "3. Adicione o IP do servidor no PlayFiver"
echo "4. Configure DNS para apontar para este servidor"
echo ""
echo -e "${BLUE}COMANDOS ÚTEIS:${NC}"
echo "Reiniciar PHP: sudo systemctl restart php8.1-fpm"
echo "Reiniciar Nginx: sudo systemctl restart nginx"
echo "Reiniciar MySQL: sudo systemctl restart mysql"
echo "Ver logs Nginx: tail -f /var/log/nginx/error.log"
echo ""
echo -e "${GREEN}✨ Servidor pronto para deploy!${NC}"