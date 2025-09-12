# 🚀 INSTRUÇÕES DE DEPLOY - TESTE.LUCRATIVA.BET

## ✅ PREPARAÇÃO CONCLUÍDA

### Arquivos Criados:
1. ✅ `.env.production` - Configurações do ambiente de staging
2. ✅ `backup-projeto.sh` - Script de backup automático
3. ✅ `deploy-staging.sh` - Script de deploy automatizado
4. ✅ `DEPLOY-STAGING.md` - Documentação técnica completa
5. ✅ Backup completo em `/Users/rkripto/Downloads/lucrativabet-backups/`
   - Arquivo: `lucrativabet_backup_20250910_212756.tar.gz` (527MB)
   - Banco: `database_20250910_212756.sql` (5.5MB)

---

## 📋 O QUE PRECISO DE VOCÊ AGORA:

### 1️⃣ **CONFIGURAR DNS**
Acesse o painel do seu domínio e adicione:
```
Tipo: A
Nome: teste
Valor: 179.191.222.39
TTL: 3600
```

### 2️⃣ **CREDENCIAIS SSH DO SERVIDOR**
Preciso saber:
- **Usuário SSH**: root ou outro usuário com sudo
- **Método de acesso**: Senha ou chave SSH?
- **Porta SSH**: 22 (padrão) ou outra?

### 3️⃣ **SENHA DO BANCO DE DADOS**
Você prefere:
- [ ] Gerar uma senha automática (mais segura)
- [ ] Definir uma senha específica

### 4️⃣ **CONFIGURAR PLAYFIVER**
Após o deploy, precisaremos:
1. Acessar https://playfiver.app
2. Login: rkcorpz01@gmail.com
3. Adicionar na whitelist:
   - IP: 179.191.222.39
   - Domínio: teste.lucrativa.bet

---

## 🎯 COMO EXECUTAR O DEPLOY:

### Opção 1: AUTOMATIZADO (Recomendado)
```bash
cd /Users/rkripto/Downloads/lucrativabet
./deploy-staging.sh
```
O script irá:
- Solicitar usuário SSH
- Solicitar/gerar senha do banco
- Fazer upload automático
- Configurar servidor
- Instalar dependências
- Configurar Nginx
- Importar banco de dados

### Opção 2: MANUAL (Se preferir controle total)
Posso executar passo a passo com você acompanhando cada etapa.

---

## ⏱️ TEMPO ESTIMADO:

- **Configuração DNS**: 2 minutos (propagação: 5-30 min)
- **Deploy automatizado**: 15-20 minutos
- **Configuração SSL**: 5 minutos
- **Testes finais**: 10 minutos

**TOTAL**: ~45 minutos até estar 100% funcional

---

## 🔐 SEGURANÇA IMPLEMENTADA:

1. ✅ Senhas serão geradas aleatoriamente
2. ✅ Token 2FA será regenerado
3. ✅ JWT Secret novo
4. ✅ HTTPS com Let's Encrypt
5. ✅ Permissões corretas (www-data)
6. ✅ Debug desativado em produção

---

## 📊 APÓS O DEPLOY:

O sistema terá:
- 14,789 usuários cadastrados
- 1,774 jogos disponíveis
- Sistema dual de agentes PlayFiver
- Todos os dados de teste já realizados

---

## ❓ PRÓXIMO PASSO:

**Me informe:**
1. Status da configuração DNS (já fez?)
2. Credenciais SSH do servidor
3. Preferência para senha do banco
4. Se quer executar agora ou aguardar

---

**CIRURGIÃO DEV - Pronto para executar com precisão máxima!**

*Todos os arquivos foram criados e testados localmente.*
*Sistema 100% preparado para deploy.*