# 🚨 LUCRATIVABET - SISTEMA DE CASSINO ONLINE 🚨

## ⚠️ LEIA PRIMEIRO - CRÍTICO!

Este é um sistema Laravel 10 + Filament Admin de cassino online **JÁ FUNCIONANDO**.
**NÃO TENTE CRIAR NADA NOVO!** Apenas faça funcionar.

---

## 🔴 INFORMAÇÕES CRÍTICAS

```json
{
  "porta_servidor": 8080,
  "url_cassino": "http://127.0.0.1:8080",
  "url_admin": "http://127.0.0.1:8080/admin",
  "credenciais_admin": {
    "email": "lucrativa@bet.com",
    "senha": "foco123@"
  },
  "banco_dados": {
    "nome": "lucrativabet",
    "arquivo_backup": "lucrativa.sql"
  },
  "arquivos_criticos_cassino": {
    "javascript": "public/build/assets/app-CRDk2_8R.js",
    "css": "public/build/assets/app-BiLvXd5_.css",
    "backup_location": "bet.sorte365.fun/public/build/assets/"
  }
}
```

---

## 🚀 SETUP RÁPIDO (5 MINUTOS)

### 1️⃣ **Instalar Dependências**
```bash
# PHP/Composer
composer install

# Node/NPM
npm install
```

### 2️⃣ **Configurar Ambiente**
```bash
# Copiar .env
cp .env.example .env 2>/dev/null || echo ".env já existe"

# VERIFICAR no .env:
# APP_URL=http://127.0.0.1:8080
# DB_DATABASE=lucrativabet
```

### 3️⃣ **Banco de Dados**
```bash
# Criar banco
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS lucrativabet;"

# Importar dados
mysql -u root -p lucrativabet < lucrativa.sql

# OU se não tiver o backup:
php artisan migrate --seed
```

### 4️⃣ **Preparar Sistema**
```bash
# Gerar chave
php artisan key:generate

# Limpar caches
php artisan optimize:clear
```

### 5️⃣ **INICIAR SERVIDOR (SEMPRE PORTA 8080)**
```bash
php artisan serve --port=8080
```

### 6️⃣ **Acessar**
- Cassino: http://127.0.0.1:8080
- Admin: http://127.0.0.1:8080/admin
- Login: lucrativa@bet.com / foco123@

---

## 🆘 PROBLEMAS COMUNS

### ❌ Cassino mostra tela preta/branca
```bash
# Executar script de correção
bash _scripts/fix-casino-files.sh

# OU manualmente:
cp bet.sorte365.fun/public/build/assets/app-CRDk2_8R.js public/build/assets/
cp bet.sorte365.fun/public/build/assets/app-BiLvXd5_.css public/build/assets/
```

### ❌ Imagens não aparecem no admin
```bash
# Verificar APP_URL
grep APP_URL .env
# DEVE ser: APP_URL=http://127.0.0.1:8080

# Limpar cache
php artisan optimize:clear
```

### ❌ Erro de login no admin
```bash
# Resetar senha
php artisan tinker
>>> $user = User::where('email', 'lucrativa@bet.com')->first();
>>> $user->password = Hash::make('foco123@');
>>> $user->save();
>>> exit
```

### ❌ npm run build quebra o cassino
**NUNCA USE npm run build!** 
Se usar por acidente, execute:
```bash
bash _scripts/fix-casino-files.sh
```

---

## 📁 ESTRUTURA IMPORTANTE

```
lucrativabet/
├── _docs/           # Documentação
├── _scripts/        # Scripts importantes
│   └── fix-casino-files.sh  # CRÍTICO: Corrige cassino
├── bet.sorte365.fun/  # BACKUP CRÍTICO - NUNCA DELETAR!
├── public/
│   └── build/
│       └── assets/
│           ├── app-CRDk2_8R.js  # JS do cassino (1.7MB)
│           └── app-BiLvXd5_.css  # CSS do cassino
└── .env  # Configurações (APP_URL=http://127.0.0.1:8080)
```

---

## ⚠️ REGRAS ABSOLUTAS

1. **NUNCA** deletar pasta `bet.sorte365.fun/`
2. **NUNCA** rodar `npm run build` (quebra o cassino)
3. **SEMPRE** usar porta 8080
4. **SEMPRE** verificar APP_URL no .env
5. **SEMPRE** usar `fix-casino-files.sh` se cassino quebrar

---

## 🎯 COMANDOS ESSENCIAIS

```bash
# Iniciar servidor (SEMPRE!)
php artisan serve --port=8080

# Limpar tudo
php artisan optimize:clear

# Corrigir cassino
bash _scripts/fix-casino-files.sh

# Ver logs
tail -f storage/logs/laravel.log
```

---

## 💡 DICA FINAL

Se **NADA funcionar**, execute:
```bash
# Script mágico de recuperação total
bash _scripts/SETUP-AUTOMATICO.sh
```

---

**Sistema desenvolvido e mantido por Cirurgião Dev**
**NÃO MODIFICAR SEM AUTORIZAÇÃO**