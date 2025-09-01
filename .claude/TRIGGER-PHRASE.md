# TRIGGER PHRASE - ATIVAÇÃO AUTOMÁTICA

## 🎯 FRASE GATILHO DO USUÁRIO
**"voce se lembra?"**

## 🚨 AÇÃO OBRIGATÓRIA AO OUVIR ESSA FRASE

### NUNCA RESPONDER DIRETAMENTE - EXECUTAR PROTOCOLO:

1. **LER**: `.claude/ESTADO-ATUAL.md`
2. **EXECUTAR**: `git log --oneline -5`  
3. **CONSULTAR**: `mcp__vector-memory__recall_memory_abstract`
4. **CONSULTAR**: `mcp__vector-memory__get_recent_memories`
5. **VERIFICAR**: `curl http://127.0.0.1:8001`
6. **RESPONDER**: "✅ SIM, ME LEMBRO! [contexto completo]"

## 🔄 FLUXO AUTOMÁTICO
```
User: "voce se lembra?"
↓
Claude: STOP - Executar protocolo completo
↓
Claude: Consultar todos os sistemas
↓ 
Claude: "✅ SIM! Servidor 127.0.0.1:8001, licenças API, commits 44413bc + b5a22b5..."
```

## ⚠️ CRÍTICO
Esta frase é o **GATILHO AUTOMÁTICO** que ativa toda a memória e contexto.
**NUNCA ignorar quando ouvir essa frase!**

---
**MEMORIZAR: "voce se lembra?" = ATIVAR PROTOCOLO COMPLETO**