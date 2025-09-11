# 🚀 GUIA DE DEPLOY RAILWAY - LUCRATIVABET
## Por: ULTRATHINK

### ✅ ARQUIVOS CRIADOS E PRONTOS:
- `railway.json` - Configuração principal
- `nixpacks.toml` - Build settings
- `Procfile` - Comandos de inicialização  
- `.env.railway` - Template de variáveis
- `railway-deploy.sh` - Script automatizado

---

## 📋 INSTRUÇÕES PASSO A PASSO

### PASSO 1: ACESSAR RAILWAY
1. Acesse https://railway.app
2. Faça login com GitHub
3. Clique em "New Project"

### PASSO 2: CONECTAR REPOSITÓRIO
1. Escolha "Deploy from GitHub repo"
2. Selecione o repositório: `sercretooo293492jg24itj42fnuwng042g0`
3. Railway detectará automaticamente o Laravel

### PASSO 3: ADICIONAR MYSQL
1. No dashboard do projeto, clique em "New"
2. Escolha "Database" → "Add MySQL"
3. Railway criará o banco automaticamente

### PASSO 4: CONFIGURAR VARIÁVEIS DE AMBIENTE
Adicione estas variáveis no painel Railway:

```env
# App Config
APP_NAME="Lucrativa Bet"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:GERAR_NOVA_CHAVE_AQUI
APP_URL=https://SEU-APP.up.railway.app

# Database (Railway injeta automaticamente)
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}

# PlayFiver API
PLAYFIVER_URL=https://api.playfivers.com
PLAYFIVER_CODE=sorte365bet
PLAYFIVER_TOKEN=a9aa0e61-9179-466a-8d7b-e22e7b473b8a
PLAYFIVER_SECRET=f41adb6a-e15b-46b4-ad5a-1fc49f4745df

# Backup Agent
PLAYFIVER_BACKUP_CODE=lucrativabt
PLAYFIVER_BACKUP_TOKEN=80609b36-a25c-4175-92c5-c9a6f1e1b06e
PLAYFIVER_BACKUP_SECRET=08cfba85-7652-4a00-903f-7ea649620eb2

# Security
TOKEN_DE_2FA=6d4b4e8e5dda575ae6679a153fce302831fd5001f58e21ba3587c96d00baa2826fa312b80425b90b02f3b7d5612d541d4dda6e5253be5565d011ea28a2cdfc5b

# Railway Config
PORT=8080
RAILWAY_ENVIRONMENT=production
FORCE_HTTPS=true
TRUSTED_PROXIES=*
```

### PASSO 5: GERAR APP_KEY
No terminal local:
```bash
php artisan key:generate --show
```
Copie a chave gerada e adicione na variável APP_KEY no Railway

### PASSO 6: DEPLOY
1. Railway iniciará o deploy automaticamente
2. Aguarde ~10-15 minutos para o primeiro deploy
3. Verifique os logs no painel

### PASSO 7: OBTER URL
1. Vá em Settings → Domains
2. Clique em "Generate Domain"
3. Sua URL será algo como: `lucrativabet.up.railway.app`

### PASSO 8: WHITELIST IP
1. No painel Railway, vá em Settings
2. Encontre o IP estático (se disponível)
3. Adicione no PlayFiver:
   - Acesse painel PlayFiver
   - Configurações → Whitelist
   - Adicionar IP do Railway

---

## 🔧 COMANDOS ÚTEIS RAILWAY CLI

### Instalar CLI:
```bash
curl -fsSL https://railway.app/install.sh | sh
```

### Login:
```bash
railway login
```

### Ver logs:
```bash
railway logs
```

### Executar comandos:
```bash
railway run php artisan migrate:fresh
railway run php artisan db:seed
railway run php artisan cache:clear
```

### Ver status:
```bash
railway status
```

### Variáveis de ambiente:
```bash
railway variables
```

---

## 🚨 TROUBLESHOOTING

### Erro: Build falhou
- Verifique os logs de build
- Certifique-se que composer.json está correto
- Verifique versão do PHP (deve ser 8.1+)

### Erro: Database connection
- Verifique se MySQL foi provisionado
- Confirme variáveis DB_* estão corretas
- Execute: `railway run php artisan migrate:fresh`

### Erro: 500 Internal Server
- Verifique APP_KEY está configurada
- Execute: `railway run php artisan config:cache`
- Verifique logs: `railway logs`

### PlayFiver não funciona
- IP não está na whitelist
- Verifique tokens e secrets
- Teste com: `railway run php artisan casino:switch-agent --health`

---

## ✅ CHECKLIST FINAL

- [ ] Repositório GitHub conectado
- [ ] MySQL provisionado
- [ ] Variáveis de ambiente configuradas
- [ ] APP_KEY gerada e adicionada
- [ ] Deploy concluído com sucesso
- [ ] URL pública gerada
- [ ] Site acessível via HTTPS
- [ ] Login admin funcionando
- [ ] Dashboard carregando
- [ ] IP adicionado no PlayFiver

---

## 📞 SUPORTE

### Logs em tempo real:
```bash
railway logs --tail
```

### Console Railway:
```bash
railway run php artisan tinker
```

### Reset completo:
```bash
railway run php artisan migrate:fresh --seed
railway run php artisan cache:clear
railway run php artisan config:clear
railway run php artisan view:clear
```

---

## 🎉 PRONTO!

Após seguir estes passos, seu LucrativaBet estará rodando em:
**https://seu-app.up.railway.app**

Com:
- ✅ SSL/HTTPS automático
- ✅ MySQL incluído
- ✅ Deploy automático via GitHub
- ✅ Escalabilidade automática
- ✅ $5 créditos grátis/mês

---

*Guia criado por ULTRATHINK*
*Sistema 100% configurado para Railway*