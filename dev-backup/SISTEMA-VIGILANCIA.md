# Sistema de Vigilância - Performance & Storage

## Objetivo
Monitorar sistema para alertar quando performance ou storage prejudicarem operação.

## Métricas Monitoradas

### 1. Armazenamento
- **Screenshots .playwright-mcp/**: Monitora crescimento
- **Logs Laravel**: Verifica tamanho de arquivos de log  
- **Cache**: Observa cache Filament e Laravel
- **Uploads**: Monitora diretório storage/uploads

### 2. Performance
- **Tempo resposta admin**: Acima de 3s = alerta
- **Carregamento CSS**: Tempo para custom-filament-theme.css
- **Widgets dashboard**: Tempo renderização > 2s
- **Memória PHP**: Acima de 128MB = alerta

### 3. Funcionalidade
- **Dashboard data**: Verificar se widgets mostram dados
- **Sidebar colors**: Garantir textos brancos, ícones verdes
- **CSS customizations**: Verificar aplicação correta

## Alertas Automáticos

### 🔴 CRÍTICO - Parar Tudo
- Dashboard sem dados (widgets vazios)
- CSS customization quebrada (cores erradas)
- Erro fatal PHP/Laravel

### 🟡 ATENÇÃO - Avisar Usuário
- Storage > 500MB em screenshots
- Logs > 50MB
- Response time > 3s consistente
- Cache > 100MB

### 🟢 INFO - Log Apenas
- Screenshots novas criadas
- CSS recarregado
- Performance normal

## Comandos Vigilância
```bash
# Storage check
du -sh storage/logs/
du -sh .playwright-mcp/
du -sh storage/uploads/

# Performance check
curl -w "@curl-format.txt" http://localhost:8080/admin

# Functionality check
curl -s http://localhost:8080/admin | grep "DASHBOARD PLATAFORMA"
```

## Relatórios
- **Diário:** Log de métricas
- **Semanal:** Análise tendências  
- **Mensal:** Otimizações sugeridas

## Ações Automáticas
- ❌ NÃO deletar automaticamente
- ❌ NÃO limpar automaticamente
- ✅ APENAS alertar usuário
- ✅ APENAS sugerir limpeza

**IMPORTANTE:** Usuário sempre decide o que fazer com os alertas.