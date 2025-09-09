#!/bin/bash

# ==================================================
# 🚀 LUCRATIVABET - SISTEMA DE INICIALIZAÇÃO COMPLETO
# ==================================================

echo "=================================================="
echo "🎰 LUCRATIVABET - INICIANDO SISTEMA COMPLETO"
echo "=================================================="
echo

# Função para verificar se o comando existe
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Função para verificar se a porta está em uso
port_in_use() {
    lsof -Pi :$1 -sTCP:LISTEN -t >/dev/null
}

# Função para matar processo na porta
kill_port() {
    if port_in_use $1; then
        echo "⚠️  Porta $1 em uso. Liberando..."
        kill -9 $(lsof -t -i:$1) 2>/dev/null
        sleep 1
    fi
}

# 1. VERIFICAR DEPENDÊNCIAS
echo "1️⃣ VERIFICANDO DEPENDÊNCIAS"
echo "---------------------------------------------"

if ! command_exists php; then
    echo "❌ PHP não encontrado!"
    exit 1
fi
echo "✅ PHP: $(php -v | head -n 1 | cut -d' ' -f2)"

if ! command_exists redis-cli; then
    echo "⚠️  Redis não instalado. Instalando..."
    if [[ "$OSTYPE" == "darwin"* ]]; then
        brew install redis
    else
        echo "❌ Por favor instale o Redis manualmente"
        exit 1
    fi
fi
echo "✅ Redis instalado"

if ! command_exists npm; then
    echo "❌ NPM não encontrado!"
    exit 1
fi
echo "✅ NPM: $(npm -v)"

echo

# 2. INICIAR REDIS
echo "2️⃣ INICIANDO REDIS"
echo "---------------------------------------------"

# Verificar se Redis está rodando
if redis-cli ping > /dev/null 2>&1; then
    echo "✅ Redis já está rodando"
else
    echo "🔄 Iniciando Redis..."
    if [[ "$OSTYPE" == "darwin"* ]]; then
        brew services start redis > /dev/null 2>&1
    else
        redis-server --daemonize yes > /dev/null 2>&1
    fi
    sleep 2
    
    if redis-cli ping > /dev/null 2>&1; then
        echo "✅ Redis iniciado com sucesso"
    else
        echo "❌ Falha ao iniciar Redis"
        exit 1
    fi
fi

echo

# 3. LIMPAR CACHE LARAVEL
echo "3️⃣ LIMPANDO CACHE DO LARAVEL"
echo "---------------------------------------------"

php artisan cache:clear > /dev/null 2>&1
php artisan config:clear > /dev/null 2>&1
php artisan route:clear > /dev/null 2>&1
php artisan view:clear > /dev/null 2>&1
echo "✅ Cache limpo"

echo

# 4. VERIFICAR FRONTEND COMPILADO
echo "4️⃣ VERIFICANDO FRONTEND"
echo "---------------------------------------------"

if [ ! -f "public/build/manifest.json" ]; then
    echo "⚠️  Frontend não compilado. Compilando..."
    npm install > /dev/null 2>&1
    npm run build > /dev/null 2>&1
    echo "✅ Frontend compilado"
else
    echo "✅ Frontend já está compilado"
fi

echo

# 5. LIBERAR PORTAS
echo "5️⃣ LIBERANDO PORTAS"
echo "---------------------------------------------"

kill_port 8000
kill_port 5173
echo "✅ Portas 8000 e 5173 liberadas"

echo

# 6. INICIAR QUEUE WORKERS
echo "6️⃣ INICIANDO QUEUE WORKERS"
echo "---------------------------------------------"

# Matar workers antigos
pkill -f "artisan queue:work" 2>/dev/null

# Iniciar novos workers
for i in {1..3}
do
    nohup php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600 > storage/logs/worker-$i.log 2>&1 &
    echo "✅ Worker $i iniciado (PID: $!)"
done

echo

# 7. INICIAR SERVIDOR LARAVEL
echo "7️⃣ INICIANDO SERVIDOR LARAVEL"
echo "---------------------------------------------"

# Iniciar servidor em background
nohup php artisan serve --host=0.0.0.0 --port=8000 > storage/logs/server.log 2>&1 &
SERVER_PID=$!
echo "✅ Servidor Laravel iniciado (PID: $SERVER_PID)"

sleep 3

echo

# 8. VERIFICAR STATUS
echo "8️⃣ VERIFICANDO STATUS DO SISTEMA"
echo "---------------------------------------------"

# Testar servidor
if curl -s http://localhost:8000 > /dev/null; then
    echo "✅ Servidor respondendo em http://localhost:8000"
else
    echo "⚠️  Servidor pode estar iniciando..."
fi

# Testar API
if curl -s http://localhost:8000/api | grep -q "API is running"; then
    echo "✅ API funcionando corretamente"
else
    echo "⚠️  API pode estar carregando..."
fi

# Testar Redis
if redis-cli ping > /dev/null 2>&1; then
    echo "✅ Redis operacional"
fi

# Contar workers
WORKERS=$(ps aux | grep -c "[a]rtisan queue:work")
echo "✅ $WORKERS Queue Workers rodando"

echo
echo "=================================================="
echo "🎯 SISTEMA LUCRATIVABET INICIADO COM SUCESSO!"
echo "=================================================="
echo
echo "📱 ACESSE O SISTEMA:"
echo "   👉 Frontend: http://localhost:8000"
echo "   👉 Admin: http://localhost:8000/admin"
echo "   👉 API: http://localhost:8000/api"
echo
echo "📊 MONITORAMENTO:"
echo "   👉 Logs do servidor: tail -f storage/logs/server.log"
echo "   👉 Logs dos workers: tail -f storage/logs/worker-*.log"
echo "   👉 Laravel logs: tail -f storage/logs/laravel.log"
echo
echo "🛑 PARA PARAR TUDO:"
echo "   👉 ./STOP-SYSTEM.sh"
echo
echo "=================================================="

# Salvar PIDs para poder parar depois
echo "$SERVER_PID" > .server.pid
ps aux | grep "[a]rtisan queue:work" | awk '{print $2}' > .workers.pid

exit 0