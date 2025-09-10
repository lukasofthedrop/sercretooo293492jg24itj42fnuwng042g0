#!/bin/bash

echo "============================================"
echo "   CRIANDO ZIP LIMPO PARA WINDOWS"
echo "============================================"

# Nome do arquivo com data/hora
FILENAME="lucrativabet-windows-$(date +%Y%m%d_%H%M%S).zip"

# Criar ZIP excluindo node_modules e vendor (serão instalados no Windows)
zip -r "../$FILENAME" . \
  -x "node_modules/*" \
  -x "vendor/*" \
  -x ".git/*" \
  -x "storage/logs/*" \
  -x "storage/framework/cache/*" \
  -x "storage/framework/sessions/*" \
  -x "storage/framework/views/*" \
  -x "*.zip" \
  -x ".DS_Store" \
  -x "Thumbs.db"

# Mostrar resultado
echo ""
echo "✅ ZIP CRIADO COM SUCESSO!"
echo "📦 Arquivo: ../$FILENAME"
echo "📏 Tamanho: $(ls -lh ../$FILENAME | awk '{print $5}')"
echo ""
echo "INSTRUÇÕES PARA WINDOWS:"
echo "1. Descompacte o arquivo"
echo "2. Abra PowerShell na pasta"
echo "3. Execute: composer install"
echo "4. Execute: npm install"
echo "5. Execute: php artisan serve --port=8000"
echo ""
echo "Credenciais: lucrativa@bet.com / foco123@"