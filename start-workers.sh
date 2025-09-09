#!/bin/bash

# Script para iniciar queue workers
echo "🚀 Iniciando Queue Workers..."

# Mata workers antigos
pkill -f "artisan queue:work" 2>/dev/null

# Limpa jobs falhados
php artisan queue:flush

# Inicia 3 workers em background
for i in {1..3}
do
    echo "Starting worker $i..."
    nohup php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600 > storage/logs/worker-$i.log 2>&1 &
done

echo "✅ 3 Queue Workers rodando em background"
echo "📊 Logs em: storage/logs/worker-*.log"

# Mostra status
sleep 2
ps aux | grep "artisan queue:work" | grep -v grep