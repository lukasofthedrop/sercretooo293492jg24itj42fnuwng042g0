# 🚀 DEPLOY MANUAL - LUCRATIVA BET CASSINO

## 📋 PASSO 1: Acessar Digital Ocean App Platform

1. Acesse: https://cloud.digitalocean.com/apps
2. Clique em **"Create App"**
3. Selecione **"GitHub"** como fonte

## 📋 PASSO 2: Conectar Repositório

1. **Repositório**: `lukasofthedrop/sercretooo293492jg24itj42fnuwng042g0`
2. **Branch**: `main`
3. **Autodeploy**: ✅ Ativado

## 📋 PASSO 3: Configurar Serviços

### Web Service:
- **Nome**: `web`
- **Tipo**: `Web Service`
- **Dockerfile Path**: `/Dockerfile`
- **HTTP Port**: `80`
- **Instance Type**: `Professional XS` ($12/mês)
- **Instance Count**: `1`

### Build Command:
```bash
docker build -t lucrativabet .
```

### Run Command:
```bash
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
```

## 📋 PASSO 4: Variáveis de Ambiente

Configure as seguintes variáveis na seção **Environment Variables**:

```env
APP_NAME=Lucrativa Bet
APP_ENV=production
APP_KEY=base64:jP1f2K0S7Xe5JkyxyP8EptDNe8w77mGYOWcEoZyH9FU=
APP_DEBUG=false
APP_URL=${_self.URL}

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=lucrativa-mysql-do-user-26563437-0.g.db.ondigitalocean.com
DB_PORT=25060
DB_DATABASE=defaultdb
DB_USERNAME=doadmin
DB_PASSWORD=AVNS_oKuuPV4DQ3PuRqz-AGj

# Cache & Session
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# JWT Configuration
JWT_SECRET=71RdDYCHDZCUQIYGU9EP8PVEp93YAYLnvlh49a3smdAKJwNj9XG0iQHiDhyQbKSk
JWT_TTL=60

# Logs
LOG_CHANNEL=stack
LOG_LEVEL=error
```

## 📋 PASSO 5: Configurar Banco de Dados

✅ **Banco já configurado**:
- **Nome**: `lucrativa-mysql`
- **Engine**: MySQL 8.0
- **Região**: NYC3
- **Status**: Online

## 📋 PASSO 6: Review e Deploy

1. Revise todas as configurações
2. **Região**: `New York 1` (NYC1) 
3. **App Name**: `lucrativabet-casino`
4. Clique em **"Create Resources"**

## 📋 PASSO 7: Aguardar Build

O processo leva cerca de 5-10 minutos:
1. **Building**: Construção da imagem Docker
2. **Deploying**: Deploy da aplicação
3. **Running**: Aplicação online

## 📋 PASSO 8: Acessar Aplicação

Após o deploy, você receberá uma URL como:
```
https://lucrativabet-casino-xxxxx.ondigitalocean.app
```

## 💰 CUSTOS ESTIMADOS

- **App Platform**: $12/mês (Professional XS)
- **Database**: $15/mês (MySQL 2GB)
- **Total**: ~$27/mês

## 🔧 COMANDOS ÚTEIS (Opcional)

Se preferir usar CLI:

```bash
# Verificar status
doctl apps list

# Ver logs
doctl apps logs <app-id> --follow

# Fazer redeploy
doctl apps create-deployment <app-id>
```

## ✅ VERIFICAÇÕES FINAIS

1. Aplicação carrega sem erros
2. Banco de dados conectado
3. Login funcionando
4. Jogos operacionais

---
**STATUS**: Pronto para deploy! 🚀