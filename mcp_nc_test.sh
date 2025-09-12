#!/bin/bash

MCP_SERVER="localhost:3000"

echo "Iniciando conexão MCP..."

# Usar netcat para manter conexão e enviar JSONs separados
{
    echo '{"jsonrpc":"2.0","method":"initialize","params":{"protocolVersion":"2024-11-05","capabilities":{},"clientInfo":{"name":"claude-code","version":"1.0.0"}},"id":1}'
    sleep 2
    echo '{"jsonrpc":"2.0","method":"tools/list","params":{},"id":2}'
} | nc "$MCP_SERVER" 2>/dev/null