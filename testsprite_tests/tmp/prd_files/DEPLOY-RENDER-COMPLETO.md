# 🚀 Deploy LucrativaBet no Render.com - Guia Completo

## 📋 Status da Preparação

✅ **Arquivos Criados:**
- `Dockerfile` - Configuração Docker otimizada com nginx e PHP-FPM
- `render.yaml` - Configuração de infraestrutura como código
- `nginx-render.conf` - Configuração nginx para produção
- `supervisord-render.conf` - Gerenciamento de processos
- `render-build.sh` - Script de build automatizado
- `deploy-render.sh` - Script de deploy facilitado
- Rota `/api/health` - Health check para monitoramento

## 🎯 Passos para Deploy

### 1️⃣ Preparar Código para Deploy

```bash
# Executar script de deploy
./deploy-render.sh
```

Este script irá:
- Verificar status do Git
- Criar commit com todas as mudanças
- Fazer push para GitHub
- Atualizar branch main se necessário

### 2️⃣ Configurar no Render.com

1. **Acesse:** https://dashboard.render.com
2. **Crie uma conta** ou faça login
3. **Clique em "New +"** → **"Web Service"**

### 3️⃣ Conectar Repositório

1. **Conecte sua conta GitHub** ao Render
2. **Selecione o repositório:** `lucrativabet`
3. **Branch:** `main`

### 4️⃣ Configurações do Serviço

**Nome:** `lucrativabet-app`
**Region:** Oregon (US West) ou Frankfurt (Europe)
**Branch:** `main`
**Root Directory:** (deixe vazio)
**Runtime:** Docker
**Instance Type:** Free

### 5️⃣ Variáveis de Ambiente

Clique em "Advanced" e adicione as seguintes variáveis:

#### 🔐 Essenciais
```env
APP_KEY=base64:gerar_nova_chave_aqui
APP_ENV=production
APP_DEBUG=false
APP_URL=https://lucrativabet-app.onrender.com
```

#### 🗄️ Banco de Dados (criado automaticamente)
```env
DB_CONNECTION=pgsql
# As outras variáveis DB_* serão preenchidas automaticamente
```

#### 🎮 PlayFiver API
```env
PLAYFIVER_URL=https://api.playfivers.com
PLAYFIVER_CODE=sorte365bet
PLAYFIVER_TOKEN=a9aa0e61-9179-466a-8d7b-e22e7b473b8a
PLAYFIVER_SECRET=f41adb6a-e15b-46b4-ad5a-1fc49f4745df

PLAYFIVER_BACKUP_CODE=lucrativabt
PLAYFIVER_BACKUP_TOKEN=80609b36-a25c-4175-92c5-c9a6f1e1b06e
PLAYFIVER_BACKUP_SECRET=08cfba85-7652-4a00-903f-7ea649620eb2
```

#### 🔒 Segurança
```env
TOKEN_DE_2FA=6d4b4e8e5dda575ae6679a153fce302831fd5001f58e21ba3587c96d00baa2826fa312b80425b90b02f3b7d5612d541d4dda6e5253be5565d011ea28a2cdfc5b
FORCE_HTTPS=true
TRUSTED_PROXIES=*
```

### 6️⃣ Deploy Inicial

1. **Clique em "Create Web Service"**
2. **Aguarde o build** (pode levar 10-15 minutos no primeiro deploy)
3. **Monitore os logs** para verificar se tudo está funcionando

### 7️⃣ Configurar Banco de Dados

O Render criará automaticamente um banco PostgreSQL gratuito. Após o deploy:

1. Vá para a aba **"Environment"**
2. Verifique se as variáveis `DB_*` foram preenchidas
3. O banco será migrado automaticamente no primeiro deploy

## 🔍 Verificação Pós-Deploy

### URLs para Testar:
- **Homepage:** `https://lucrativabet-app.onrender.com`
- **Admin:** `https://lucrativabet-app.onrender.com/admin`
- **API Health:** `https://lucrativabet-app.onrender.com/api/health`

### Credenciais Admin:
- **Email:** admin@admin.com
- **Senha:** Ultra@Mega#2025Power!

## 🛠️ Comandos Úteis

### Executar Comandos no Container:
```bash
# Via Render Shell
php artisan cache:clear
php artisan config:cache
php artisan migrate:fresh --seed
```

### Logs em Tempo Real:
- Acesse a aba **"Logs"** no dashboard do Render

## ⚠️ Troubleshooting

### Erro de Migração:
```bash
# No Render Shell
php artisan migrate:fresh --force
php artisan db:seed --force
```

### Erro 500:
```bash
# Verificar logs
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Assets não carregando:
```bash
npm run build
php artisan storage:link
```

## 📊 Monitoramento

### Health Check:
- URL: `/api/health`
- Frequência: A cada 30 segundos
- Timeout: 10 segundos

### Métricas Disponíveis:
- CPU e Memória (Dashboard Render)
- Logs de aplicação
- Uptime e disponibilidade

## 🔄 Atualizações Futuras

Para fazer deploy de novas versões:

```bash
# Local
git add .
git commit -m "feat: nova funcionalidade"
git push origin main

# O Render fará deploy automático
```

## 🆘 Suporte

### Links Úteis:
- [Documentação Render](https://render.com/docs)
- [Status Render](https://status.render.com)
- [Community Forum](https://community.render.com)

### Problemas Comuns:
1. **Build falha:** Verifique o Dockerfile e dependências
2. **App não inicia:** Verifique as variáveis de ambiente
3. **Banco não conecta:** Aguarde propagação das credenciais
4. **Performance lenta:** Normal no plano gratuito (cold starts)

## 🎯 Checklist Final

- [ ] Código enviado para GitHub
- [ ] Render conectado ao repositório
- [ ] Variáveis de ambiente configuradas
- [ ] Build concluído com sucesso
- [ ] Banco de dados migrado
- [ ] Health check respondendo
- [ ] Admin acessível
- [ ] Jogos funcionando

---

**Última Atualização:** 11/09/2025
**Status:** 🟢 Pronto para Deploy
**Desenvolvido por:** ULTRATHINK