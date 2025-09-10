#!/bin/bash

echo "============================================"
echo "   PREPARANDO ZIP OTIMIZADO PARA WINDOWS"
echo "============================================"
echo ""

# Nome do arquivo com timestamp
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
FILENAME="lucrativabet-windows-$TIMESTAMP.zip"

echo "📦 Criando ZIP otimizado..."
echo "⏳ Isso pode demorar alguns minutos..."
echo ""

# Criar ZIP excluindo arquivos desnecessários
zip -r "../$FILENAME" . \
  -x "node_modules/*" \
  -x "vendor/*" \
  -x ".git/*" \
  -x ".playwright-mcp/*" \
  -x "storage/logs/*" \
  -x "storage/framework/cache/*" \
  -x "storage/framework/sessions/*" \
  -x "storage/framework/views/*" \
  -x "*.zip" \
  -x ".DS_Store" \
  -x "Thumbs.db" \
  -x "*.sh" \
  -x ".env.backup*" \
  -x ".env.OLD" \
  -x ".env.bak" \
  -x "memory.sqlite" \
  -x ".claude/*" \
  -x ".workers.pid" \
  -x ".server.pid" \
  2>/dev/null

echo ""
echo "✅ ZIP CRIADO COM SUCESSO!"
echo ""
echo "📊 INFORMAÇÕES DO ARQUIVO:"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "📦 Arquivo: ../$FILENAME"
echo "📏 Tamanho: $(ls -lh ../$FILENAME | awk '{print $5}')"
echo "📍 Local: $(cd .. && pwd)/$FILENAME"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "🚀 PRÓXIMOS PASSOS NO WINDOWS:"
echo "1. Transfira o arquivo $FILENAME"
echo "2. Descompacte em uma pasta"
echo "3. Siga as instruções do PROMPT-WINDOWS.txt"
echo ""
echo "✨ Arquivo pronto para transferência!"