#!/bin/bash
# SCRIPT PERMANENTE PARA NUNCA MAIS ERRAR O ARQUIVO DO CASSINO
# ARQUIVO CORRETO: app-CRDk2_8R.js (1.7MB)
# NUNCA USAR: app-e613bb2d.js

echo "🔧 VERIFICANDO E CORRIGINDO ARQUIVOS DO CASSINO..."

# DELETAR ARQUIVOS ERRADOS SE EXISTIREM
if [ -f "public/build/assets/app-e613bb2d.js" ]; then
    echo "🗑️ DELETANDO arquivo ERRADO app-e613bb2d.js..."
    rm -f public/build/assets/app-e613bb2d.js
fi

if [ -f "public/build/assets/app-21d810a1.css" ]; then
    echo "🗑️ DELETANDO CSS errado app-21d810a1.css..."
    rm -f public/build/assets/app-21d810a1.css
fi

# Verificar se o arquivo correto existe no build
if [ ! -f "public/build/assets/app-CRDk2_8R.js" ]; then
    echo "❌ ARQUIVO DO CASSINO INCORRETO OU AUSENTE!"
    echo "✅ Restaurando arquivo CORRETO do backup..."
    cp bet.sorte365.fun/public/build/assets/app-CRDk2_8R.js public/build/assets/
    cp bet.sorte365.fun/public/build/assets/app-BiLvXd5_.css public/build/assets/
    echo "✅ Arquivos restaurados com sucesso!"
else
    # Verificar tamanho do arquivo (deve ser ~1.7MB)
    SIZE=$(du -k "public/build/assets/app-CRDk2_8R.js" | cut -f1)
    if [ $SIZE -lt 1500 ]; then
        echo "⚠️  Arquivo existe mas tamanho incorreto. Restaurando..."
        cp bet.sorte365.fun/public/build/assets/app-CRDk2_8R.js public/build/assets/
        cp bet.sorte365.fun/public/build/assets/app-BiLvXd5_.css public/build/assets/
    else
        echo "✅ Arquivo do cassino está CORRETO (app-CRDk2_8R.js - 1.7MB)"
    fi
fi

# Verificar app.blade.php
if grep -q "app-e613bb2d.js" resources/views/layouts/app.blade.php; then
    echo "❌ app.blade.php está com arquivo ERRADO!"
    echo "✅ Corrigindo para usar app-CRDk2_8R.js..."
    sed -i '' 's/app-e613bb2d\.js/app-CRDk2_8R.js/g' resources/views/layouts/app.blade.php
    sed -i '' 's/app-21d810a1\.css/app-BiLvXd5_.css/g' resources/views/layouts/app.blade.php
    echo "✅ app.blade.php corrigido!"
fi

echo "🎯 VERIFICAÇÃO COMPLETA - CASSINO USANDO ARQUIVOS CORRETOS!"
echo "📝 LEMBRETE: app-CRDk2_8R.js (1.7MB) é o ÚNICO arquivo correto!"