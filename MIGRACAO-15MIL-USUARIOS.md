# 🚨 PLANO DE MIGRAÇÃO - 15 MIL USUÁRIOS

## ⚠️ STATUS ATUAL DO SISTEMA

### Banco de Dados Atual:
- **Usuários atuais**: 11 registros
- **Sistema**: 100% funcional
- **Backup**: ✅ Criado em `backup_before_migration_*.sql`

### Estrutura da Tabela Users:
```sql
CAMPOS OBRIGATÓRIOS:
- id (auto)
- name
- email (UNIQUE!)
- password (hash bcrypt)
- role_id (default: 3)
- created_at
- updated_at

CAMPOS OPCIONAIS:
- last_name
- cpf
- phone
- avatar
- inviter
- inviter_code
- affiliate_revenue_share (default: 2)
- affiliate_cpa (default: 10)
- affiliate_baseline (default: 40)
- language
- two_factor_secret
- two_factor_recovery_codes
```

---

## ❗ PERGUNTAS CRÍTICAS ANTES DE PROSSEGUIR

### 1. FORMATO DOS DADOS
Em que formato estão os 15 mil usuários?
- [ ] CSV
- [ ] JSON
- [ ] SQL
- [ ] Excel
- [ ] Outro: _______

### 2. CAMPOS DISPONÍVEIS
Quais campos você tem dos usuários antigos?
- [ ] Nome
- [ ] Email
- [ ] CPF
- [ ] Telefone
- [ ] Senha (texto plano ou hash?)
- [ ] Data de cadastro
- [ ] Saldo
- [ ] Outros: _______

### 3. TRATAMENTO DE SENHAS
Como estão as senhas?
- [ ] Texto plano (precisaremos criptografar)
- [ ] MD5 (precisaremos re-hash)
- [ ] Bcrypt (compatível)
- [ ] Outro: _______

### 4. DUPLICATAS
O que fazer com emails duplicados?
- [ ] Ignorar duplicatas
- [ ] Atualizar existentes
- [ ] Adicionar sufixo (_2, _3)
- [ ] Outro: _______

---

## 🔐 PLANO DE MIGRAÇÃO SEGURA

### FASE 1: PREPARAÇÃO
1. ✅ Backup do banco atual (FEITO)
2. ⏳ Receber arquivo com 15 mil usuários
3. ⏳ Analisar estrutura dos dados
4. ⏳ Validar campos obrigatórios

### FASE 2: SCRIPT DE MIGRAÇÃO
```php
// Estrutura do script
1. Ler arquivo de origem
2. Validar cada registro:
   - Email válido
   - Email único
   - Campos obrigatórios preenchidos
3. Preparar dados:
   - Hash de senhas (se necessário)
   - Formatar CPF
   - Limpar telefones
4. Inserir em lotes de 500
5. Log de erros
```

### FASE 3: TESTE PILOTO
1. Extrair 10 usuários do arquivo
2. Executar migração teste
3. Verificar:
   - Login funciona
   - Dados corretos
   - Sistema estável
4. Rollback se houver problemas

### FASE 4: MIGRAÇÃO COMPLETA
1. Desativar sistema temporariamente
2. Executar migração em lotes:
   - 500 usuários por vez
   - Pausas de 1 segundo entre lotes
   - Log detalhado
3. Verificações:
   - Count total
   - Integridade dos dados
   - Teste de login aleatório
4. Reativar sistema

### FASE 5: PÓS-MIGRAÇÃO
1. Verificar logs de erro
2. Relatório de migração:
   - Total migrados
   - Erros encontrados
   - Duplicatas
3. Backup pós-migração
4. Monitorar sistema 24h

---

## ⚠️ RISCOS E MITIGAÇÕES

### RISCOS:
1. **Crash do banco**: Lotes pequenos (500)
2. **Emails duplicados**: Validação prévia
3. **Senhas incompatíveis**: Re-hash automático
4. **Sistema ficar lento**: Migração fora do horário de pico
5. **Perda de dados**: Múltiplos backups

### COMANDOS DE EMERGÊNCIA:
```bash
# Restaurar backup se der problema
mysql -u root lucrativabet < backup_before_migration_*.sql

# Verificar quantos foram migrados
mysql -u root -e "SELECT COUNT(*) FROM lucrativabet.users"

# Deletar usuários migrados (manter os 11 originais)
mysql -u root -e "DELETE FROM lucrativabet.users WHERE id > 100"
```

---

## 📊 ESTIMATIVAS

### TEMPO ESTIMADO:
- Preparação: 30 minutos
- Teste piloto: 15 minutos
- Migração completa: 2-3 horas
- Verificação: 30 minutos
- **TOTAL**: 3-4 horas

### RECURSOS NECESSÁRIOS:
- MySQL rodando
- PHP memory_limit: 512M
- Espaço em disco: 500MB
- Backup: 100MB

---

## ✅ CHECKLIST PRÉ-MIGRAÇÃO

- [ ] Backup criado
- [ ] Arquivo com 15 mil usuários recebido
- [ ] Formato do arquivo confirmado
- [ ] Script de migração criado
- [ ] Teste com 10 usuários OK
- [ ] Horário de migração definido
- [ ] Usuário avisado sobre downtime

---

## 🚨 AGUARDANDO INFORMAÇÕES

**POR FAVOR, FORNEÇA:**
1. O arquivo com os 15 mil usuários
2. Formato do arquivo (CSV, JSON, etc)
3. Quais campos estão disponíveis
4. Como estão as senhas
5. Horário preferido para migração

**SÓ PROSSEGUIREMOS APÓS ESTAS INFORMAÇÕES!**