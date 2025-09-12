#!/bin/bash

# Script para interagir com o servidor MCP via JSON-RPC
MCP_SERVER="http://localhost:3000"

# Função para enviar comandos JSON-RPC
send_command() {
    local method="$1"
    local params="$2"
    local id="$3"
    
    local json_payload="{\"jsonrpc\":\"2.0\",\"method\":\"$method\",\"params\":$params,\"id\":$id}"
    
    echo "Enviando comando: $method"
    echo "Payload: $json_payload"
    
    curl -s -X POST "$MCP_SERVER" \
        -H "Content-Type: application/json" \
        -H "Accept: application/json" \
        -H "Accept: text/event-stream" \
        -d "$json_payload"
    
    echo -e "\n---\n"
}

# Inicializar o servidor
echo "1. Inicializando servidor MCP..."
send_command "initialize" "{\"protocolVersion\":\"2024-11-05\",\"capabilities\":{},\"clientInfo\":{\"name\":\"claude-code\",\"version\":\"1.0.0\"}}" 1

# Listar ferramentas
echo "2. Listando ferramentas disponíveis..."
send_command "tools/list" "{}" 2