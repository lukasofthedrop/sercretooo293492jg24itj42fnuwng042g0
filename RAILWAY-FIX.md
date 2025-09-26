# Correção do Problema de Deploy no Railway

## Problema Identificado

O site está retornando um erro HTTP 500 com a mensagem:
```
filemtime(): stat failed for /app/storage/framework/views/44fb48915db0428a65481620c7285aa0.php
```

Isso ocorre porque a variável de ambiente `VIEW_COMPILED_PATH` não está sendo definida corretamente no ambiente Railway, fazendo com que o Laravel procure os arquivos de cache de views no diretório errado.

## Soluções Implementadas

### 1. Modificação do entrypoint-fixed.sh

Foi modificado o arquivo `deploy/entrypoint-fixed.sh` para:
- Definir explicitamente a variável de ambiente `VIEW_COMPILED_PATH="/tmp/views"`
- Adicionar verificação adicional do diretório de views

### 2. Script de Correção

Foi criado o script `fix-railway-deployment.sh` que pode ser executado manualmente para corrigir o problema.

## Como Fazer o Deploy

### Opção 1: Usando o Railway CLI (se o token estiver funcionando)

```bash
# Fazer login no Railway (se necessário)
railway login

# Conectar ao projeto
railway link --project 24f08b86-11f2-4209-8738-ff3d518464c0

# Fazer o deploy
railway up
```

### Opção 2: Via Interface Web do Railway

1. Acesse o dashboard do Railway
2. Selecione o projeto `lucrativabet-web-production`
3. Vá para a aba "Variables"
4. Verifique se a variável `VIEW_COMPILED_PATH` está definida como `/tmp/views`
5. Se não estiver, adicione essa variável
6. Clique em "Deploy" para fazer um novo deploy

### Opção 3: Usando Git

Se o projeto estiver conectado a um repositório Git:

```bash
# Commit das alterações
git add .
git commit -m "Fix Railway deployment view cache issue"

# Push para o repositório
git push
```

O Railway fará o deploy automaticamente.

## Verificação

Após o deploy, verifique se o site está funcionando:

```bash
curl -I https://lucrativabet-web-production.up.railway.app/
```

O status deve ser 200 OK em vez de 500 Internal Server Error.

## Variáveis de Ambiente Importantes

Certifique-se de que as seguintes variáveis de ambiente estão configuradas corretamente no Railway:

```
VIEW_COMPILED_PATH=/tmp/views
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:Tq10ZYNF64apwpe8e9vFaTFMW1wvM0BGMU1mgmfKL0I=
```

## Logs

Se o problema persistir, verifique os logs do Railway:

```bash
railway logs
```

Ou através da interface web do Railway.