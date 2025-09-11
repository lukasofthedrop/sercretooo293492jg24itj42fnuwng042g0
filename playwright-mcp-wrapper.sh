#!/bin/bash

# Wrapper script para MCP Playwright com reconexão automática
# Este script garante que o servidor permaneça rodando

LOG_FILE="/tmp/playwright-mcp.log"

# Função para limpar processos antigos
cleanup() {
    echo "[$(date)] Limpando processos antigos..." >> "$LOG_FILE"
    pkill -f "@playwright/mcp" 2>/dev/null
    pkill -f "playwright-mcp" 2>/dev/null
    sleep 1
}

# Função para iniciar o servidor
start_server() {
    echo "[$(date)] Iniciando servidor Playwright MCP..." >> "$LOG_FILE"
    
    # Garantir que estamos no diretório correto
    cd /Users/rkripto/Downloads/lucrativabet
    
    # Executar com npx diretamente
    exec npx -y @playwright/mcp@latest 2>&1 | tee -a "$LOG_FILE"
}

# Tratamento de sinais para reconexão
trap 'cleanup; start_server' HUP TERM

# Limpar processos antigos antes de iniciar
cleanup

# Iniciar o servidor
start_server