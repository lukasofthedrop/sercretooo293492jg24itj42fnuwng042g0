#!/bin/bash

echo "=== TESTE DE CARGA - 10.000 REQUESTS ==="
echo "Testando capacidade do sistema..."

# Teste simples com curl
URL="http://127.0.0.1:8080"
CONCURRENT=100
TOTAL=10000

echo "URL: $URL"
echo "Requisições simultâneas: $CONCURRENT"
echo "Total de requisições: $TOTAL"
echo ""

# Usando Apache Bench se disponível
if command -v ab &> /dev/null; then
    echo "Executando teste com Apache Bench..."
    ab -n $TOTAL -c $CONCURRENT -g results.tsv $URL/ 2>&1 | grep -E "Requests per second|Time per request|Failed requests|Percentage"
else
    echo "Apache Bench não instalado. Usando curl..."
    START=$(date +%s)
    
    for i in $(seq 1 $CONCURRENT); do
        (
            for j in $(seq 1 $(($TOTAL / $CONCURRENT))); do
                curl -s -o /dev/null -w "%{http_code}" $URL > /dev/null 2>&1
            done
        ) &
    done
    
    wait
    END=$(date +%s)
    DURATION=$((END - START))
    RPS=$((TOTAL / DURATION))
    
    echo "Duração: ${DURATION}s"
    echo "Requests por segundo: ~${RPS}"
fi

echo ""
echo "Teste concluído!"
