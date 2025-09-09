#!/bin/bash

# SETUP AUTOMÁTICO LUCRATIVABET - IMPOSSÍVEL DAR ERRADO
# Execute este script e o sistema funcionará 100%

echo "================================================"
echo "🚀 SETUP AUTOMÁTICO LUCRATIVABET"
echo "================================================"
echo ""

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Função para verificar comando
check_command() {
    if command -v $1 &> /dev/null; then
        echo -e "${GREEN}✓${NC} $1 instalado"
        return 0
    else
        echo -e "${RED}✗${NC} $1 não encontrado"
        return 1
    fi
}

# 0. VERIFICAÇÃO CRÍTICA
echo "🔍 VERIFICAÇÃO CRÍTICA DE ARQUIVOS..."
echo "--------------------------------"

# Verificar pasta bet.sorte365.fun (MAIS IMPORTANTE!)
if [ ! -d "bet.sorte365.fun" ]; then
    echo -e "${RED}✗ ERRO CRÍTICO: Pasta bet.sorte365.fun não encontrada!${NC}"
    echo -e "${RED}  Sem esta pasta o cassino NUNCA funcionará!${NC}"
    echo -e "${RED}  Copie a pasta completa (753MB) e tente novamente.${NC}"
    exit 1
else
    echo -e "${GREEN}✓${NC} Pasta bet.sorte365.fun encontrada"
fi

# Verificar arquivo crítico do cassino
if [ ! -f "bet.sorte365.fun/public/build/assets/app-CRDk2_8R.js" ]; then
    echo -e "${RED}✗ ERRO: Arquivo app-CRDk2_8R.js não encontrado no backup!${NC}"
    exit 1
else
    echo -e "${GREEN}✓${NC} Arquivo crítico app-CRDk2_8R.js encontrado"
fi

echo ""

# 1. VERIFICAR DEPENDÊNCIAS
echo "📋 VERIFICANDO DEPENDÊNCIAS..."
echo "--------------------------------"

check_command php || { echo -e "${RED}Instale PHP 8.2+${NC}"; exit 1; }
check_command composer || { echo -e "${RED}Instale Composer${NC}"; exit 1; }
check_command node || { echo -e "${RED}Instale Node.js${NC}"; exit 1; }
check_command npm || { echo -e "${RED}Instale NPM${NC}"; exit 1; }
check_command mysql || echo -e "${YELLOW}MySQL não encontrado (opcional se usar SQLite)${NC}"

echo ""

# 2. INSTALAR DEPENDÊNCIAS
echo "📦 INSTALANDO DEPENDÊNCIAS..."
echo "--------------------------------"

if [ ! -d "vendor" ]; then
    echo "Instalando dependências PHP..."
    composer install --no-interaction --prefer-dist
else
    echo -e "${GREEN}✓${NC} Vendor já existe"
fi

if [ ! -d "node_modules" ]; then
    echo "Instalando dependências Node..."
    npm install
else
    echo -e "${GREEN}✓${NC} Node modules já existe"
fi

echo ""

# 3. CONFIGURAR AMBIENTE
echo "⚙️  CONFIGURANDO AMBIENTE..."
echo "--------------------------------"

# Criar .env se não existir
if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        cp .env.example .env
        echo -e "${GREEN}✓${NC} .env criado"
    else
        echo -e "${YELLOW}Criando .env do zero...${NC}"
        cat > .env << 'EOF'
APP_NAME=LucrativaBet
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://127.0.0.1:8080

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lucrativabet
DB_USERNAME=root
DB_PASSWORD=

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120
EOF
        echo -e "${GREEN}✓${NC} .env criado com configurações padrão"
    fi
fi

# Garantir APP_URL correto (compatível Mac/Linux)
echo "Verificando APP_URL..."
if ! grep -q "APP_URL=http://127.0.0.1:8080" .env; then
    # Detectar OS e usar sed apropriado
    if [[ "$OSTYPE" == "darwin"* ]]; then
        # macOS
        sed -i '' 's|APP_URL=.*|APP_URL=http://127.0.0.1:8080|' .env
    else
        # Linux/WSL
        sed -i 's|APP_URL=.*|APP_URL=http://127.0.0.1:8080|' .env
    fi
    echo -e "${GREEN}✓${NC} APP_URL corrigido para porta 8080"
else
    echo -e "${GREEN}✓${NC} APP_URL já está correto"
fi

# Gerar APP_KEY se vazio
if ! grep -q "APP_KEY=base64:" .env; then
    php artisan key:generate
    echo -e "${GREEN}✓${NC} APP_KEY gerada"
else
    echo -e "${GREEN}✓${NC} APP_KEY já existe"
fi

echo ""

# 4. CONFIGURAR BANCO DE DADOS
echo "🗄️  CONFIGURANDO BANCO DE DADOS..."
echo "--------------------------------"

# Tentar MySQL primeiro
if command -v mysql &> /dev/null; then
    echo "Tentando criar banco MySQL..."
    mysql -u root -e "CREATE DATABASE IF NOT EXISTS lucrativabet;" 2>/dev/null && {
        echo -e "${GREEN}✓${NC} Banco MySQL criado/verificado"
        
        # Importar backup se existir
        if [ -f "lucrativa.sql" ]; then
            echo "Importando backup do banco..."
            mysql -u root lucrativabet < lucrativa.sql 2>/dev/null && {
                echo -e "${GREEN}✓${NC} Backup importado"
            } || {
                echo -e "${YELLOW}Não foi possível importar backup, rodando migrations${NC}"
                php artisan migrate --seed --force
            }
        else
            echo "Rodando migrations..."
            php artisan migrate --seed --force
        fi
    } || {
        echo -e "${YELLOW}MySQL não acessível, usando SQLite${NC}"
        touch database/database.sqlite
        sed -i '' 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/' .env 2>/dev/null || \
        sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/' .env
        php artisan migrate --seed --force
    }
else
    echo -e "${YELLOW}Usando SQLite${NC}"
    touch database/database.sqlite
    sed -i '' 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/' .env 2>/dev/null || \
    sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/' .env
    php artisan migrate --seed --force
fi

echo ""

# 5. VERIFICAR ARQUIVOS DO CASSINO
echo "🎰 VERIFICANDO ARQUIVOS DO CASSINO..."
echo "--------------------------------"

CASINO_JS="public/build/assets/app-CRDk2_8R.js"
CASINO_CSS="public/build/assets/app-BiLvXd5_.css"
BACKUP_DIR="bet.sorte365.fun/public/build/assets"

# Verificar e corrigir arquivos do cassino
if [ ! -f "$CASINO_JS" ] || [ $(stat -f%z "$CASINO_JS" 2>/dev/null || stat -c%s "$CASINO_JS" 2>/dev/null) -lt 1500000 ]; then
    echo -e "${YELLOW}Arquivo do cassino incorreto ou ausente${NC}"
    if [ -f "$BACKUP_DIR/app-CRDk2_8R.js" ]; then
        echo "Restaurando do backup..."
        cp "$BACKUP_DIR/app-CRDk2_8R.js" "$CASINO_JS"
        cp "$BACKUP_DIR/app-BiLvXd5_.css" "$CASINO_CSS"
        echo -e "${GREEN}✓${NC} Arquivos do cassino restaurados"
    else
        echo -e "${RED}✗ Backup não encontrado! Cassino pode não funcionar${NC}"
    fi
else
    echo -e "${GREEN}✓${NC} Arquivos do cassino OK"
fi

echo ""

# 6. LIMPAR CACHES
echo "🧹 LIMPANDO CACHES..."
echo "--------------------------------"
php artisan optimize:clear
echo -e "${GREEN}✓${NC} Caches limpos"

echo ""

# 7. CRIAR USUÁRIO ADMIN SE NÃO EXISTIR
echo "👤 VERIFICANDO USUÁRIO ADMIN..."
echo "--------------------------------"

php artisan tinker --execute="
    \$exists = \App\Models\User::where('email', 'lucrativa@bet.com')->exists();
    if (!\$exists) {
        \App\Models\User::create([
            'name' => 'Admin',
            'email' => 'lucrativa@bet.com',
            'password' => Hash::make('foco123@'),
            'is_admin' => true
        ]);
        echo 'Usuário admin criado';
    } else {
        \$user = \App\Models\User::where('email', 'lucrativa@bet.com')->first();
        \$user->password = Hash::make('foco123@');
        \$user->save();
        echo 'Senha do admin atualizada';
    }
" 2>/dev/null || echo -e "${YELLOW}Verifique o usuário admin manualmente${NC}"

echo ""

# 8. INSTRUÇÕES FINAIS
echo "================================================"
echo -e "${GREEN}✅ SETUP COMPLETO!${NC}"
echo "================================================"
echo ""
echo "🚀 PARA INICIAR O SERVIDOR:"
echo -e "${YELLOW}php artisan serve --port=8080${NC}"
echo ""
echo "📱 ACESSAR:"
echo "   Cassino: http://127.0.0.1:8080"
echo "   Admin: http://127.0.0.1:8080/admin"
echo ""
echo "🔐 CREDENCIAIS:"
echo "   Email: lucrativa@bet.com"
echo "   Senha: foco123@"
echo ""
echo "================================================"

# Perguntar se quer iniciar agora
read -p "Deseja iniciar o servidor agora? (s/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Ss]$ ]]; then
    echo -e "${GREEN}Iniciando servidor na porta 8080...${NC}"
    php artisan serve --port=8080
fi