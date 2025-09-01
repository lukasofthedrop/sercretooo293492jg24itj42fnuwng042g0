# CHECKLIST OBRIGATÓRIO - PRIMEIRA AÇÃO SEMPRE

## ⚠️ NUNCA RESPONDER SEM EXECUTAR ESTE CHECKLIST

### 1️⃣ LER ESTADO ATUAL
- [ ] `Read(.claude/ESTADO-ATUAL.md)` 

### 2️⃣ CONSULTAR GIT
- [ ] `Bash(git log --oneline -5)`
- [ ] `Bash(git status)` 

### 3️⃣ CONSULTAR MEMÓRIAS
- [ ] `mcp__vector-memory__recall_memory_abstract`
- [ ] `mcp__vector-memory__get_recent_memories`

### 4️⃣ VERIFICAR SERVIDOR
- [ ] `Bash(curl -s -o /dev/null -w "%{http_code}" http://127.0.0.1:8001)`

### 5️⃣ SÓ ENTÃO RESPONDER
- Com contexto completo
- Mencionando o que consultei
- Sendo específico sobre o estado atual

---
**SE NÃO FIZER ESTE CHECKLIST = RESPOSTA INVÁLIDA**