#!/bin/bash
# Wrapper script for Playwright MCP

# Set up NVM
export NVM_DIR="$HOME/.nvm"
[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"

# Use Node v22.19.0
nvm use v22.19.0 > /dev/null 2>&1

# Set PATH
export PATH="/Users/rkripto/.nvm/versions/node/v22.19.0/bin:$PATH"

# Run Playwright MCP
cd /Users/rkripto/Downloads/lucrativabet
exec node node_modules/@playwright/mcp/cli.js "$@"