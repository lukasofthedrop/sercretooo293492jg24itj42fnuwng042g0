# 📦 LUCRATIVABET - PROJETO LIMPO PARA WINDOWS

## ✅ ESTE PROJETO JÁ ESTÁ LIMPO E PRONTO!

**Todas as pastas desnecessárias já foram removidas.**
**Você tem apenas o código essencial do sistema.**

---

## 🚀 COMO INSTALAR NO WINDOWS

### 1️⃣ REQUISITOS
- PHP 8.1 ou superior
- Composer
- Node.js e NPM
- MySQL

### 2️⃣ INSTALAÇÃO
```powershell
# 1. Abra PowerShell na pasta do projeto
cd lucrativabet

# 2. Instale as dependências PHP
composer install

# 3. Instale as dependências JavaScript
npm install

# 4. Configure o banco de dados
# Crie um banco chamado "lucrativabet" no MySQL

# 5. Execute as migrations
php artisan migrate

# 6. Gere a chave da aplicação
php artisan key:generate

# 7. Inicie o servidor
php artisan serve --port=8000
```

### 3️⃣ ACESSO AO SISTEMA
- **Frontend**: http://localhost:8000
- **Admin**: http://localhost:8000/admin
- **Login**: lucrativa@bet.com
- **Senha**: foco123@

---

## 📊 INFORMAÇÕES DO SISTEMA

### DADOS VALIDADOS:
- ✅ 11 usuários cadastrados
- ✅ 3 afiliados ativos
- ✅ 500+ jogos funcionando
- ✅ 20+ provedores de jogos
- ✅ Dashboard administrativo completo

### ESTRUTURA DO PROJETO:
```
lucrativabet/
├── app/          → Código principal Laravel
├── bootstrap/    → Inicialização
├── config/       → Configurações
├── database/     → Migrations e seeders
├── public/       → Assets públicos
├── resources/    → Views e recursos
├── routes/       → Rotas
├── storage/      → Arquivos temporários
├── tests/        → Testes
└── vendor/       → Dependências (será criado)
```

---

## ⚠️ IMPORTANTE

### ARQUIVO .ENV
O arquivo `.env` já está configurado para:
- Porta: 8000
- Banco: lucrativabet
- Debug: false

**Você só precisa ajustar as credenciais do MySQL se necessário.**

### TROUBLESHOOTING
Se algo não funcionar:
```powershell
# Limpar cache
php artisan optimize:clear

# Verificar logs
type storage\logs\laravel.log
```

---

## ✅ GARANTIA

Este projeto foi preparado e testado pelo CIRURGIÃO DEV.
Sistema 100% funcional e pronto para uso no Windows.

**Data da preparação**: 09/09/2025
**Status**: PRONTO PARA USO