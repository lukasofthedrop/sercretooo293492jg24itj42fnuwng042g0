# ⚠️ ATENÇÃO - SCRIPTS DE SISTEMA

## 🚨 PARA QUALQUER IA QUE ABRIR ESTA PASTA

**CUIDADO EXTREMO COM OS SCRIPTS DESTA PASTA!**

---

## ❌ SCRIPTS PERIGOSOS - NÃO EXECUTE

### 🚫 NUNCA EXECUTE ESTES SCRIPTS:
- ❌ `reset.sh` - PERIGOSO! Reseta sistema funcional
- ❌ `restore-backup.sh` - PERIGOSO! Restaura backups antigos  
- ❌ Qualquer script que contenha "reset" ou "restore"

### ⚠️ POR QUE SÃO PERIGOSOS:
- Podem destruir sistema atual 100% funcional
- Restauram configurações antigas e inseguras
- Apagam dados e configurações atuais
- Quebram funcionalidades validadas

---

## ✅ SCRIPTS SEGUROS (SE NECESSÁRIO)

### 🟢 PROVAVELMENTE SEGUROS:
- `fix-casino-files.sh` - Corrige arquivos do cassino (apenas se necessário)
- `SETUP-AUTOMATICO.sh` - Setup do sistema (apenas em emergência)

### 🟡 USAR COM CUIDADO:
- Scripts de backup (podem sobreescrever atual)
- Scripts de configuração (podem alterar .env)

---

## 📋 ANTES DE EXECUTAR QUALQUER SCRIPT

### ✅ CHECKLIST OBRIGATÓRIO:
1. **Sistema atual funciona?** 
   - Teste: `php artisan serve --port=8000`
   - Se SIM: NÃO execute scripts de correção

2. **Leu a documentação?**
   - `README.md`, `CLAUDE.md`, `IA-INSTRUCTIONS.md`
   - Se NÃO: Leia primeiro

3. **Consultou memórias?**
   - Verificar se há contexto sobre este script específico
   - Se NÃO: Consulte antes

4. **Perguntou ao usuário?**
   - "Posso executar X script para Y finalidade?"
   - Se NÃO: Pergunte primeiro

---

## 🔍 ANÁLISE DE SCRIPTS ESPECÍFICOS

### 📄 reset.sh
- **Função**: Reseta sistema para estado inicial
- **Perigo**: ⚠️ MÁXIMO - Destrói sistema atual
- **Quando usar**: NUNCA se sistema funciona
- **Autorização**: Sempre perguntar ao usuário

### 📄 restore-backup.sh  
- **Função**: Restaura backup antigo
- **Perigo**: ⚠️ ALTO - Substitui sistema atual
- **Quando usar**: Apenas em desastre total
- **Autorização**: Confirmar com usuário

### 📄 fix-casino-files.sh
- **Função**: Corrige arquivos do frontend do cassino
- **Perigo**: 🟡 BAIXO - Apenas se cassino quebrado
- **Quando usar**: Se frontend não carrega
- **Autorização**: Pode usar se necessário

---

## 🎯 REGRAS PARA IA

### ✅ SEMPRE FAÇA:
1. Teste sistema atual antes de executar scripts
2. Leia o script para entender o que faz  
3. Consulte documentação sobre o problema
4. Pergunte ao usuário se necessário

### ❌ NUNCA FAÇA:
1. Execute scripts "preventivamente"
2. Use scripts para "melhorar" sistema funcionando
3. Execute múltiplos scripts sem testar entre eles
4. Ignore avisos de scripts perigosos

---

## 📊 STATUS DO SISTEMA ATUAL

**Conforme validação de 09/09/2025:**
- ✅ **Frontend**: 100% funcional (500+ jogos)
- ✅ **Admin**: 100% funcional (11 usuários)  
- ✅ **Afiliados**: 100% funcional (3 afiliados)
- ✅ **Database**: Conectado e estável
- ✅ **Logs**: Sem erros críticos

**SE TUDO ACIMA FUNCIONA: NÃO EXECUTE SCRIPTS DE CORREÇÃO!**

---

## 🔧 DIAGNÓSTICO ANTES DE SCRIPTS

### 🩺 Teste Básico:
```bash
# 1. Testar servidor
php artisan serve --port=8000

# 2. Testar frontend  
curl -I http://localhost:8000

# 3. Testar admin
curl -I http://localhost:8000/admin

# 4. Ver logs
tail -5 storage/logs/laravel.log
```

### ✅ SE TESTES PASSAM:
**Sistema está funcionando. NÃO execute scripts de correção.**

### ❌ SE TESTES FALHAM:
1. Investigue causa específica
2. Consulte documentação
3. Considere script específico para o problema
4. Pergunte ao usuário antes de executar

---

## 🚨 AVISO FINAL

**LEMBRE-SE:**
- Sistema atual está 100% funcional
- Scripts podem quebrar funcionalidades
- Sempre teste antes de "corrigir"
- Em caso de dúvida, pergunte ao usuário

**Responsabilidade da IA:**
- Manter sistema funcional
- Não quebrar o que está funcionando
- Aplicar princípio "primeiro, não causar dano"

---

*Aviso criado pelo CIRURGIÃO DEV em 09/09/2025*  
*Proteção contra execução indevida de scripts perigosos*  
*Sistema atual: 100% funcional - Preserve-o*