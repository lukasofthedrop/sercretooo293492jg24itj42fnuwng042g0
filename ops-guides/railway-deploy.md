# Deploy na Railway

Guia completo para publicar o LucrativaBet na Railway com foco em escalabilidade e segregação de responsabilidades.

## 1. Visão Geral da Arquitetura
- **Imagem Docker única** (construída pelo Dockerfile atualizado) usada por três serviços:
  - `web`: Nginx + PHP-FPM servindo HTTP.
  - `queue`: workers `php artisan queue:work`.
  - `scheduler`: laço executando `php artisan schedule:run`.
- **Serviços gerenciados Railway**:
  - MySQL (banco primário).
  - Redis (cache, sessões e filas).
  - Armazenamento de arquivos (S3/R2 recomendado para produção).
- **Variáveis** controladas pela Railway (`MYSQL*`, `REDIS*`) e segredos definidos manualmente.

## 2. Preparação Local
1. Verifique os assets estáticos: `public/build/**` deve estar presente (não rode `npm run build`).
2. Gere a imagem localmente para validar (opcional):
   ```bash
   docker build -t lucrativabet:latest .
   ```
   O processo usa os assets existentes e aplica `php-optimization.ini` automaticamente.

## 3. Provisionamento na Railway
### 3.1. Banco de Dados
- Crie um serviço MySQL na Railway.
- Use os dumps da pasta `backups/` para restaurar dados:
  ```bash
  mysql -h $MYSQLHOST -u $MYSQLUSER -p$MYSQLPASSWORD $MYSQLDATABASE < backups/lucrativabet_20250918_011609.sql
  ```
- Ative backups automáticos diários pela Railway.

### 3.2. Redis
- Crie um serviço Redis (planos Production recomendados).
- Após provisionado, associe `REDISHOST`, `REDISPORT`, `REDISPASSWORD` às variáveis do projeto.

### 3.3. Serviços de Aplicação
Crie três serviços com a mesma imagem:

| Serviço | APP_ROLE | RUN_MIGRATIONS | Comando personalizado |
|---------|----------|----------------|-----------------------|
| `lucrativabet-web` | `web` | `0` | padrão (entrypoint roda nginx+php) |
| `lucrativabet-queue` | `queue` | `0` | opcionalmente definir `QUEUE_WORK_OPTIONS` |
| `lucrativabet-scheduler` | `scheduler` | `0` | `SCHEDULER_INTERVAL` para frequência |

> Para rodar migrations automaticamente em um deploy específico, defina `RUN_MIGRATIONS=1` temporariamente no serviço `web` e remova após concluir.

### 3.4. Variáveis de Ambiente
1. Copie `env.railway.example` para o painel da Railway.
2. Preencha `APP_KEY` (obrigatório) com `php artisan key:generate --show`.
3. Ajuste `APP_URL`, `MAIL_*`, integrações (Stripe, OAuth etc.).
4. Configure `FILESYSTEM_DISK`:
   - `local` para início rápido (utiliza `storage/app/public`).
   - `s3` quando houver bucket dedicado (preencha `AWS_*`).

### 3.5. Storage Persistente
- Conecte um bucket S3/R2 e ajuste `FILESYSTEM_DISK=s3`.
- Certifique-se de rodar `php artisan storage:link` (já é executado no entrypoint web).

## 4. Pipeline / Deploy
1. Conecte o repositório GitHub à Railway ou use `railway up`.
2. A Railway compilará a imagem usando o `Dockerfile` (build sem etapa de frontend).
3. Após o primeiro deploy:
   - Acesse o serviço `web` e verifique logs no painel.
   - Execute migrations manualmente se necessário (`railway run php artisan migrate --force`).
   - Valide `/admin/login` e a home pública.

## 5. Observabilidade e Rotina
- **Logs**: utilize `railway logs` por serviço.
- **Monitoramento**: configure alertas de CPU/RAM; para filas, monitore tamanho via `php artisan queue:failed`.
- **Tarefas agendadas**: confirmadas pelo serviço `scheduler`. Ajuste `SCHEDULER_INTERVAL` conforme necessidade.
- **Backups**: habilite no MySQL e exporte periodicamente para storage externo.

## 6. Escalabilidade Futuramente
- **Horizontal**: aumente réplicas do serviço `web` (sessões permanecem íntegras por Redis).
- **Vertical**: suba planos de CPU/RAM ou use autoscaling da Railway.
- **Cache**: considere Redis cluster para grandes volumes.
- **Assets**: mover `public/` para CDN (Cloudflare) reduz carga no app.

## 7. Pós-Deploy Checklist
- [ ] `APP_KEY` preenchido e `APP_DEBUG=false`.
- [ ] Credenciais admin confirmadas no ambiente produtivo.
- [ ] Banco restaurado + migrations recentes executadas.
- [ ] Redis ligado e filas processando.
- [ ] Scheduler disparando jobs recorrentes.
- [ ] Certificado HTTPS configurado (Railway Domains > TLS).
- [ ] Configuração de e-mail validada (`MAILER` > teste).

Com essas etapas concluídas, o projeto fica pronto para operar na Railway com caminho claro para expansão futura.
