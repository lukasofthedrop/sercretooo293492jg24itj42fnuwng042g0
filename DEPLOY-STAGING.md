# 📋 DEPLOY STAGING - TESTE.LUCRATIVA.BET

## 🔐 INFORMAÇÕES SENSÍVEIS NECESSÁRIAS

### 1. ACESSO SSH
```
Servidor: 179.191.222.39
Usuário: [NECESSÁRIO]
Senha/Chave: [NECESSÁRIO]
```

### 2. BANCO DE DADOS
```
Nome BD: lucrativabet_teste
Usuário BD: lucrativa_user
Senha BD: [SERÁ GERADA]
```

### 3. TOKENS A GERAR NO SERVIDOR
- TOKEN_DE_2FA: [Gerar com: openssl rand -hex 16]
- JWT_SECRET: [Gerar com: php artisan jwt:secret]
- PUSHER_APP_SECRET: [Obter do Pusher]
- STRIPE_SECRET: [Obter do Stripe]

## 📁 ESTRUTURA DO PROJETO

### Tamanho Total: ~2.3GB (com vendor/node_modules)
### Tamanho Compactado: ~50MB (sem vendor/node_modules)

### Diretórios Principais:
```
/app              - Lógica da aplicação
/bootstrap        - Inicialização do Laravel
/config           - Configurações
/database         - Migrations e seeds
/public           - Arquivos públicos
/resources        - Views e assets
/routes           - Definição de rotas
/storage          - Arquivos temporários e logs
/vendor           - Dependências PHP (será instalado no servidor)
```

## 🛠️ REQUISITOS DO SERVIDOR

### Software Necessário:
- PHP 8.2+ com extensões:
  - bcmath, ctype, json, mbstring, openssl
  - pdo_mysql, tokenizer, xml, curl, zip
- MySQL 8.0+ ou MariaDB 10.3+
- Redis Server
- Nginx ou Apache
- Composer 2.x
- Git
- Certbot (Let's Encrypt)

### Extensões PHP Verificadas Localmente:
✅ bcmath
✅ ctype
✅ gd
✅ json
✅ mbstring
✅ mysqli/pdo_mysql
✅ openssl
✅ tokenizer
✅ xml

## 📊 DADOS DO SISTEMA

### Estatísticas Atuais:
- Total Usuários: 14,789
- Total Jogos: 1,774
- Depósitos Aprovados: 36
- Total Apostas: 163
- Total Saques: 7

### Agentes PlayFiver Configurados:
1. **sorte365bet** (Principal)
   - Token: a9aa0e61-9179-466a-8d7b-e22e7b473b8a
   - Secret: f41adb6a-e15b-46b4-ad5a-1fc49f4745df
   - Saldo Local: R$ 53,152.40

2. **lucrativabt** (Backup)
   - Token: 80609b36-a25c-4175-92c5-c9a6f1e1b06e
   - Secret: 08cfba85-7652-4a00-903f-7ea649620eb2
   - Saldo Local: R$ 0.00

## ⚠️ ARQUIVOS SENSÍVEIS (NÃO INCLUIR NO DEPLOY)

- .env (usar .env.production)
- .env.backup
- .env.testing
- storage/logs/*.log
- storage/framework/cache/*
- storage/framework/sessions/*
- node_modules/
- vendor/
- .git/

## 🔄 PROCESSO DE DEPLOY

### Ordem de Execução:
1. Configurar DNS (teste.lucrativa.bet → 179.191.222.39)
2. Preparar servidor com dependências
3. Upload do projeto compactado
4. Instalar dependências com Composer
5. Configurar banco de dados
6. Executar migrations
7. Configurar Nginx/Apache
8. Instalar certificado SSL
9. Otimizar aplicação
10. Configurar PlayFiver whitelist

## 📝 NOTAS IMPORTANTES

1. **Migrar Banco**: Exportar dados locais e importar no servidor
2. **Redis**: Configurar para cache, sessions e queue
3. **Permissões**: www-data:www-data para storage e bootstrap/cache
4. **SSL**: Obrigatório para produção (Let's Encrypt)
5. **PlayFiver**: Adicionar IP e domínio na whitelist

## 🚨 CHECKLIST PRÉ-DEPLOY

- [ ] Backup local completo
- [ ] .env.production configurado
- [ ] DNS configurado
- [ ] Acesso SSH confirmado
- [ ] Credenciais banco de dados
- [ ] Painel PlayFiver acessível

---

**Documento criado por CIRURGIÃO DEV**
Data: $(date)
Projeto: LucrativaBet
Destino: teste.lucrativa.bet