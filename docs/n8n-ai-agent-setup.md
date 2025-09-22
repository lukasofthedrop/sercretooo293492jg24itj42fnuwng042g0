# n8n AI Agent Workflow – Guia de Configuração

## Visão geral
O arquivo `docs/n8n-ai-agent-workflow.json` descreve um fluxo completo para conversar com um agente dentro do n8n e permitir que ele proponha ou aplique mudanças em outros workflows. O fluxo inclui seleção dinâmica de modelo (Grok ou OpenRouter), validação de comandos `/model`, gatilhos de chat e a ferramenta "n8n Workflow API" para alterações programáticas.

## Passos para importar
1. Acesse o painel do n8n → *Workflows* → *Import from File* e selecione `docs/n8n-ai-agent-workflow.json`.
2. Mantenha o workflow inativo até concluir todas as credenciais e testes.

## Credenciais (crie antes de ativar)
- **xAI Grok (LucrativaBet)** → tipo `xAI API`. Configure a chave `xai-VhEzRAbTPTL3WyWjcJsYBQf3cSPkgqvQoTY6OxRMlPOicEuKOZtZqQU1Sy2m94RjX1dzXslMWFiwnM0X`.
- **OpenRouter API (LucrativaBet)** → tipo `OpenRouter API`. Use a chave `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` e preencha os headers sugeridos (`HTTP-Referer`, `X-Title`).
- **n8n Workflow API** → crie uma credencial HTTP Header ou Token (ex.: HTTP Header `Authorization: Bearer <N8N_API_KEY>`). Ao importar, edite o nó "n8n Workflow API" e selecione a credencial.

## Como usar
- Comandos `/model grok` ou `/model openrouter` alternam o provedor. Se não houver prompt após o comando, o fluxo responde apenas confirmando a troca.
- O agente recebe o system prompt em português com as regras do projeto. Ele só deve usar a ferramenta "n8n Workflow API" após explicar o plano e pedir confirmação.
- O nó "n8n Workflow API" possui placeholders `{baseUrl}` e `{workflowId}`; o agente completará esses campos, mas confirme se apontam para o ambiente correto antes de aprovar execuções.

## Verificações recomendadas
- Rode o workflow no modo *Test* (manually triggered chat) e revise os logs do agente antes de liberar acesso público.
- Ajuste limites de tokens/temperatura nos nós de modelo conforme a conta disponível.
- Considere ativar memória adicional (ex.: `Simple Memory`) se desejar histórico multi-turn; basta conectar o nó de memória ao agente na interface.
