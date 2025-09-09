#!/bin/bash

echo "=== TESTE RÁPIDO DE SEGURANÇA ==="
echo ""

SCORE=0

# 1. dotenv-editor removido
if ! grep -q "jackiedo/dotenv-editor" composer.json 2>/dev/null; then
    echo "✅ dotenv-editor removido"
    ((SCORE++))
else
    echo "❌ dotenv-editor ainda presente"
fi

# 2. CORS configurado
if grep -q "http://127.0.0.1:8080" config/cors.php 2>/dev/null; then
    echo "✅ CORS configurado corretamente"
    ((SCORE++))
else
    echo "❌ CORS não configurado"
fi

# 3. Upload validation
if grep -q "allowedMimes" app/Helpers/Core.php 2>/dev/null; then
    echo "✅ Validação de upload implementada"
    ((SCORE++))
else
    echo "❌ Validação de upload não encontrada"
fi

# 4. Password policy
if grep -q "min:12" app/Http/Controllers/Api/Auth/AuthController.php 2>/dev/null; then
    echo "✅ Política de senha forte"
    ((SCORE++))
else
    echo "❌ Política de senha fraca"
fi

# 5. Logs limpos (apenas em Log:: statements)
if ! grep -q "Log.*request->all()" app/Http/Controllers/AureoLinkController.php 2>/dev/null; then
    echo "✅ Logs sensíveis limpos"
    ((SCORE++))
else
    echo "❌ Logs ainda expondo dados"
fi

# 6. 2FA files
if [ -f app/Http/Controllers/TwoFactorController.php ]; then
    echo "✅ 2FA Controller existe"
    ((SCORE++))
else
    echo "❌ 2FA não implementado"
fi

# 7. Activity log
if [ -f config/activitylog.php ]; then
    echo "✅ Activity log configurado"
    ((SCORE++))
else
    echo "❌ Activity log não configurado"
fi

# 8. Session security
if grep -q "'encrypt' => true" config/session.php 2>/dev/null; then
    echo "✅ Sessões criptografadas"
    ((SCORE++))
else
    echo "❌ Sessões não criptografadas"
fi

# 9. Security headers
if [ -f app/Http/Middleware/SecurityHeaders.php ]; then
    echo "✅ Security Headers middleware"
    ((SCORE++))
else
    echo "❌ Security Headers não existe"
fi

# 10. HTTPS config
if [ -f CONFIGURAR-HTTPS.md ]; then
    echo "✅ Instruções HTTPS criadas"
    ((SCORE++))
else
    echo "❌ Instruções HTTPS não encontradas"
fi

echo ""
echo "================================"
echo "SCORE TOTAL: $SCORE/10"
echo "================================"

if [ $SCORE -eq 10 ]; then
    echo "🎉 PERFEITO! Todas as correções aplicadas!"
elif [ $SCORE -ge 8 ]; then
    echo "✅ ÓTIMO! Sistema bem protegido."
elif [ $SCORE -ge 6 ]; then
    echo "⚠️ BOM, mas ainda precisa melhorias."
else
    echo "❌ INSUFICIENTE! Correções necessárias."
fi