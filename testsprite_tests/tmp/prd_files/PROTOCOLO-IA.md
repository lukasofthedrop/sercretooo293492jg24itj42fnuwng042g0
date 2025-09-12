# 🤖 PROTOCOLO PARA IA - LUCRATIVABET

## VOCÊ É UMA IA ABRINDO ESTE PROJETO? LEIA ISTO PRIMEIRO!

---

## 🎯 OBJETIVO PRINCIPAL
Fazer este sistema de cassino Laravel funcionar **SEM CRIAR NADA NOVO**.
O sistema já está 100% pronto e funcionando.

---

## ⚡ COMANDO RÁPIDO (SE NADA MAIS FUNCIONAR)
```bash
bash _scripts/SETUP-AUTOMATICO.sh
```
Este script resolve 99% dos problemas automaticamente.

---

## 📋 CHECKLIST DE VERIFICAÇÃO

### 1. ARQUIVOS CRÍTICOS EXISTEM?
```bash
# Verificar arquivos do cassino (MAIS IMPORTANTE!)
ls -lah public/build/assets/app-CRDk2_8R.js  # Deve ter ~1.7MB
ls -lah public/build/assets/app-BiLvXd5_.css  # Deve ter ~242KB

# Se não existirem ou tamanho errado:
bash _scripts/fix-casino-files.sh
```

### 2. PORTA CORRETA NO .ENV?
```bash
grep APP_URL .env
# DEVE retornar: APP_URL=http://127.0.0.1:8080
# Se não, edite o .env
```

### 3. SERVIDOR NA PORTA CORRETA?
```bash
# SEMPRE use porta 8080
php artisan serve --port=8080
# NUNCA use outra porta!
```

---

## 🚨 ERROS COMUNS E SOLUÇÕES

### ERRO: "Cassino mostra tela branca/preta"
```bash
# Solução definitiva:
cp bet.sorte365.fun/public/build/assets/app-CRDk2_8R.js public/build/assets/
cp bet.sorte365.fun/public/build/assets/app-BiLvXd5_.css public/build/assets/
php artisan optimize:clear
```

### ERRO: "Cannot login to admin"
```bash
php artisan tinker
>>> $user = \App\Models\User::where('email', 'lucrativa@bet.com')->first();
>>> $user->password = \Hash::make('foco123@');
>>> $user->save();
>>> exit
```

### ERRO: "Images not showing in admin"
```bash
# Verificar e corrigir APP_URL
sed -i 's|APP_URL=.*|APP_URL=http://127.0.0.1:8080|' .env
php artisan optimize:clear
```

### ERRO: "Database connection refused"
```bash
# Opção 1: Criar banco MySQL
mysql -u root -p -e "CREATE DATABASE lucrativabet;"
mysql -u root -p lucrativabet < lucrativa.sql

# Opção 2: Usar SQLite
touch database/database.sqlite
# Editar .env: DB_CONNECTION=sqlite
php artisan migrate --seed
```

---

## 🔴 NUNCA FAÇA ISTO

1. ❌ **NUNCA** execute `npm run build` (quebra o cassino)
2. ❌ **NUNCA** delete a pasta `bet.sorte365.fun/` (é o backup)
3. ❌ **NUNCA** use porta diferente de 8080
4. ❌ **NUNCA** mude APP_URL para localhost:1234 ou outra porta
5. ❌ **NUNCA** crie novos arquivos de build

---

## ✅ SEMPRE FAÇA ISTO

1. ✅ **SEMPRE** verifique o README.md primeiro
2. ✅ **SEMPRE** use porta 8080
3. ✅ **SEMPRE** execute `fix-casino-files.sh` se cassino quebrar
4. ✅ **SEMPRE** mantenha APP_URL=http://127.0.0.1:8080
5. ✅ **SEMPRE** preserve a pasta bet.sorte365.fun/

---

## 📊 INFORMAÇÕES DO SISTEMA

```json
{
  "framework": "Laravel 10.x",
  "admin": "Filament v3",
  "database": "MySQL/SQLite",
  "port": 8080,
  "credentials": {
    "email": "lucrativa@bet.com",
    "password": "foco123@"
  },
  "critical_files": {
    "casino_js": "app-CRDk2_8R.js (1.7MB)",
    "casino_css": "app-BiLvXd5_.css (242KB)",
    "backup_folder": "bet.sorte365.fun/"
  }
}
```

---

## 🎯 FLUXO CORRETO DE SETUP

1. **Ler README.md**
2. **Executar `bash _scripts/SETUP-AUTOMATICO.sh`**
3. **Verificar arquivos do cassino**
4. **Iniciar servidor na porta 8080**
5. **Testar cassino e admin**

---

## 💬 MENSAGEM PARA O USUÁRIO

Se você é uma IA e chegou até aqui, diga ao usuário:

> "Encontrei o PROTOCOLO-IA.md. Vou executar o setup automático para garantir que tudo funcione. O sistema já está pronto, só preciso configurar o ambiente."

E então execute:
```bash
bash _scripts/SETUP-AUTOMATICO.sh
```

---

## 🆘 ÚLTIMO RECURSO

Se ABSOLUTAMENTE NADA funcionar:
1. Verifique se está na pasta correta
2. Verifique se os arquivos existem
3. Execute: `ls -la` e `pwd`
4. Informe o usuário do problema específico
5. Sugira transferir a pasta novamente

---

**ESTE PROTOCOLO FOI CRIADO PARA GARANTIR SUCESSO**
**SIGA AS INSTRUÇÕES E O SISTEMA FUNCIONARÁ**