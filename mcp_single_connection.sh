#!/bin/bash

MCP_SERVER="http://localhost:3000"

# Criar payload de inicialização
INIT_PAYLOAD='{"jsonrpc":"2.0","method":"initialize","params":{"protocolVersion":"2024-11-05","capabilities":{},"clientInfo":{"name":"claude-code","version":"1.0.0"}},"id":1}'

# Criar payload para listar ferramentas
TOOLS_PAYLOAD='{"jsonrpc":"2.0","method":"tools/list","params":{},"id":2}'

# Enviar ambos os comandos em uma única conexão
echo "Iniciando comunicação com MCP..."
(
    echo "$INIT_PAYLOAD"
    sleep 1
    echo "$TOOLS_PAYLOAD"
) | curl -s -X POST "$MCP_SERVER" \
    -H "Content-Type: application/json" \
    -H "Accept: application/json" \
    -H "Accept: text/event-stream" \
    -d @-