#!/bin/bash

# ==================================================
# 🛑 LUCRATIVABET - PARAR SISTEMA
# ==================================================

echo "=================================================="
echo "🛑 PARANDO SISTEMA LUCRATIVABET"
echo "=================================================="
echo

# 1. PARAR SERVIDOR LARAVEL
echo "1️⃣ PARANDO SERVIDOR LARAVEL"
echo "---------------------------------------------"

if [ -f .server.pid ]; then
    SERVER_PID=$(cat .server.pid)
    if kill -0 $SERVER_PID 2>/dev/null; then
        kill -9 $SERVER_PID
        echo "✅ Servidor parado (PID: $SERVER_PID)"
    else
        echo "⚠️  Servidor não estava rodando"
    fi
    rm .server.pid
else
    # Tentar encontrar e matar de qualquer forma
    pkill -f "artisan serve" 2>/dev/null
    echo "✅ Processos do servidor finalizados"
fi

echo

# 2. PARAR QUEUE WORKERS
echo "2️⃣ PARANDO QUEUE WORKERS"
echo "---------------------------------------------"

if [ -f .workers.pid ]; then
    while IFS= read -r pid; do
        if kill -0 $pid 2>/dev/null; then
            kill -9 $pid
            echo "✅ Worker parado (PID: $pid)"
        fi
    done < .workers.pid
    rm .workers.pid
else
    # Tentar matar todos os workers
    pkill -f "artisan queue:work" 2>/dev/null
    echo "✅ Todos os workers finalizados"
fi

echo

# 3. LIMPAR PROCESSOS ÓRFÃOS
echo "3️⃣ LIMPANDO PROCESSOS ÓRFÃOS"
echo "---------------------------------------------"

# Matar qualquer processo Laravel restante
pkill -f "artisan" 2>/dev/null

# Liberar portas
lsof -t -i:8000 | xargs kill -9 2>/dev/null
lsof -t -i:5173 | xargs kill -9 2>/dev/null

echo "✅ Processos órfãos limpos"
echo "✅ Portas liberadas"

echo

# 4. VERIFICAR REDIS (não parar, apenas mostrar status)
echo "4️⃣ STATUS DO REDIS"
echo "---------------------------------------------"

if redis-cli ping > /dev/null 2>&1; then
    echo "ℹ️  Redis ainda está rodando (não foi parado)"
    echo "   Para parar Redis: brew services stop redis"
else
    echo "ℹ️  Redis não está rodando"
fi

echo
echo "=================================================="
echo "✅ SISTEMA LUCRATIVABET PARADO COM SUCESSO!"
echo "=================================================="
echo
echo "Para reiniciar: ./START-SYSTEM.sh"
echo
echo "=================================================="

exit 0