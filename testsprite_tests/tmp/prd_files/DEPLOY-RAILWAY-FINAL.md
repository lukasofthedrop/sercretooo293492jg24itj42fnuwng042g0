# 🚀 DEPLOY RAILWAY - INSTRUÇÕES FINAIS

## STATUS ATUAL
✅ Arquivos Railway configurados e prontos
✅ Railway CLI instalado
⚠️ Repositório GitHub com arquivos grandes (impedindo push)

## OPÇÃO 1: DEPLOY VIA GITHUB (Recomendado)

### Passos para limpar repositório:
```bash
# 1. Criar novo repositório limpo no GitHub
# Acesse: https://github.com/new
# Nome: lucrativabet-clean

# 2. No terminal local:
cd /Users/rkripto/Downloads
cp -r lucrativabet lucrativabet-clean
cd lucrativabet-clean

# 3. Remover histórico Git e arquivos grandes
rm -rf .git
rm -rf _backups
rm -rf storage/logs/*.log
rm -rf bootstrap/cache/*
find . -name "*.sqlite" -delete
find . -name "*.tar.gz" -delete

# 4. Inicializar novo repositório
git init
git add .
git commit -m "Initial commit - LucrativaBet clean for Railway"
git branch -M main
git remote add origin https://github.com/SEU_USUARIO/lucrativabet-clean.git
git push -u origin main
```

### Deploy no Railway via GitHub:
1. Acesse: https://railway.app
2. Login com GitHub
3. New Project → Deploy from GitHub repo
4. Selecione: lucrativabet-clean
5. Railway detectará Laravel automaticamente

## OPÇÃO 2: DEPLOY DIRETO (Sem GitHub)

### No terminal:
```bash
# 1. Login no Railway (abrirá navegador)
railway login

# 2. Criar novo projeto
railway init -n lucrativabet

# 3. Adicionar MySQL
railway add mysql

# 4. Deploy direto
railway up
```

## VARIÁVEIS DE AMBIENTE RAILWAY

Copie e cole no painel Railway:

```env
# App
APP_NAME="Lucrativa Bet"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:SEU_APP_KEY_AQUI
APP_URL=https://SEU-APP.up.railway.app

# Database (Railway injeta automaticamente)
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}

# PlayFiver Principal
PLAYFIVER_URL=https://api.playfivers.com
PLAYFIVER_CODE=sorte365bet
PLAYFIVER_TOKEN=a9aa0e61-9179-466a-8d7b-e22e7b473b8a
PLAYFIVER_SECRET=f41adb6a-e15b-46b4-ad5a-1fc49f4745df

# PlayFiver Backup
PLAYFIVER_BACKUP_CODE=lucrativabt
PLAYFIVER_BACKUP_TOKEN=80609b36-a25c-4175-92c5-c9a6f1e1b06e
PLAYFIVER_BACKUP_SECRET=08cfba85-7652-4a00-903f-7ea649620eb2

# Security
TOKEN_DE_2FA=6d4b4e8e5dda575ae6679a153fce302831fd5001f58e21ba3587c96d00baa2826fa312b80425b90b02f3b7d5612d541d4dda6e5253be5565d011ea28a2cdfc5b

# Mail
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha-app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@lucrativa.bet
MAIL_FROM_NAME="Lucrativa Bet"

# Railway
PORT=8080
RAILWAY_ENVIRONMENT=production
FORCE_HTTPS=true
TRUSTED_PROXIES=*
```

## GERAR APP_KEY
```bash
php artisan key:generate --show
```
Copie o resultado e cole em APP_KEY no Railway

## APÓS DEPLOY

### 1. Obter URL pública:
- Dashboard Railway → Settings → Domains
- Generate Domain
- Será algo como: lucrativabet-production.up.railway.app

### 2. Atualizar APP_URL:
- Nas variáveis Railway, atualize APP_URL com a URL gerada

### 3. Executar migrações:
```bash
railway run php artisan migrate:fresh --seed
```

### 4. Criar admin:
```bash
railway run php artisan make:filament-user
```

### 5. Whitelist IP PlayFiver:
- Obter IP Railway: Dashboard → Settings
- Adicionar no PlayFiver: Painel → Configurações → Whitelist

## COMANDOS ÚTEIS

### Ver logs:
```bash
railway logs --tail
```

### Executar comandos:
```bash
railway run php artisan cache:clear
railway run php artisan config:cache
railway run php artisan view:cache
```

### Status:
```bash
railway status
```

## PROBLEMAS COMUNS

### Erro 500:
```bash
railway run php artisan key:generate
railway run php artisan config:cache
```

### Banco não conecta:
- Verificar se MySQL foi provisionado
- Confirmar variáveis DB_*

### PlayFiver não funciona:
- IP não está na whitelist
- Verificar tokens/secrets

## SUPORTE

Email: suporte@lucrativa.bet
WhatsApp: +55 11 99999-9999

---

**Sistema preparado por ULTRATHINK**
**100% configurado para Railway**