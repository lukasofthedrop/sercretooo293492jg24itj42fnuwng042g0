# 🔧 Configuração Manual do Render MCP

## ⚠️ AÇÃO NECESSÁRIA

O Render MCP precisa ser adicionado manualmente como servidor HTTP.

## Passos para Configurar:

### 1. Editar arquivo de configuração do Claude

Abra o arquivo `/Users/rkripto/.claude/claude_desktop_config.json` e adicione:

```json
"render": {
  "command": "npx",
  "args": ["-y", "@smithery/mcp-server-render"],
  "env": {
    "RENDER_API_KEY": "rnd_sm1zBqBoEp5As7Nhce0YPPQn0Stb"
  }
}
```

OU se preferir configuração HTTP direta:

```json
"render": {
  "type": "http",
  "url": "https://mcp.render.com/mcp",
  "headers": {
    "Authorization": "Bearer rnd_sm1zBqBoEp5As7Nhce0YPPQn0Stb"
  }
}
```

### 2. Salvar e Reiniciar Claude

Após adicionar a configuração:
1. Salve o arquivo
2. Feche completamente o Claude
3. Abra novamente

### 3. Verificar Conexão

Execute:
```bash
/mcp list
```

Deve mostrar: `Render: ✓ Connected`

## 📦 Arquivos de Deploy Prontos:

✅ **Dockerfile** - Container Laravel otimizado
✅ **render.yaml** - Configuração completa com PostgreSQL
✅ **build.sh** - Script de build
✅ **docker-entrypoint.sh** - Script de inicialização
✅ **.env.render** - Variáveis de ambiente

## 🚀 Deploy no Render Dashboard:

1. Acesse: https://dashboard.render.com
2. Clique em **New** → **Web Service**
3. Conecte o GitHub: `lukasofthedrop/sercretooo293492jg24itj42fnuwng042g0`
4. Branch: `railway-deploy`
5. O Render detectará automaticamente o `render.yaml`

## API Key do Render:
```
rnd_sm1zBqBoEp5As7Nhce0YPPQn0Stb
```

---

**Sistema preparado por ULTRATHINK**
**Aguardando configuração manual do MCP**