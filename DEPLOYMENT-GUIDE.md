# 🚀 LucrativaBet - Deployment Production-Ready

## 📋 Status Atual
- ✅ Aplicação funcionando perfeitamente em `http://localhost:8000`
- ✅ Health check: `/api/health` retornando 200 OK
- ✅ Frontend completo carregando
- ✅ Banco de dados PostgreSQL conectado
- ✅ Todos os arquivos de deployment criados

## 🎯 Estratégia de Deploy Recomendada: AWS Lightsail

### Por que AWS Lightsail?
- **Performance Consistente** - Sem cold starts
- **Custo Acessível** - $39/mês total
- **Controle Total** - Liberdade de configuração
- **Confiabilidade** - 99.9% uptime SLA
- **Escalabilidade** - Fácil upgrade

### 💰 Custo Estimado
- **Instância**: $24/mês (2GB RAM, 2 vCPU, 60GB SSD)
- **Banco de Dados**: $15/mês (1GB RAM, 1 vCPU, 40GB SSD)
- **Total**: $39/mês

## 📁 Arquivos de Configuração Criados

### 1. `docker-compose.yml`
- PostgreSQL 15
- Redis 7
- Laravel App
- Nginx com SSL
- Rede dedicada
- Volumes persistentes

### 2. `nginx.conf`
- Otimizado para Laravel
- SSL/TLS com HTTP/2
- Security headers
- Rate limiting
- Cache estático
- Gzip compression

### 3. `.env.production`
- Configuração production
- Variáveis de ambiente seguras
- Otimizações para produção

### 4. `docker/postgres/init.sql`
- Extensões PostgreSQL
- Configurações iniciais

## 🛠️ Próximos Passos para Deploy

### Passo 1: Criar Conta AWS Lightsail (10 min)
1. Acessar [AWS Lightsail](https://lightsail.aws.amazon.com/)
2. Criar conta (se necessário)
3. Gerar credenciais de acesso

### Passo 2: Criar Instância Ubuntu 22.04 (15 min)
1. Escolher plano: $24/mês (2GB RAM, 2 vCPU, 60GB SSD)
2. Região: us-east-1 (Virgínia)
3. Configurar SSH key
4. Iniciar instância

### Passo 3: Instalar Docker e Dependências (20 min)
```bash
# Conectar via SSH
ssh -i ~/.ssh/lucrativabet.pem ubuntu@INSTANCE_IP

# Atualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Instalar Docker Compose
sudo curl -L "https://github.com/docker/compose/releases/download/v2.20.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
sudo chmod +x /usr/local/bin/docker-compose

# Instalar Nginx (para proxy reverso)
sudo apt install nginx -y

# Instalar Certbot (SSL)
sudo apt install certbot python3-certbot-nginx -y
```

### Passo 4: Fazer Deploy da Aplicação (30 min)
```bash
# Clonar repositório
git clone https://github.com/lukasofthedrop/sercretooo293492jg24itj42fnuwng042g0.git lucrativabet
cd lucrativabet

# Copiar arquivo de ambiente
cp .env.production .env

# Editar senha do banco de dados
nano .env  # Alterar DB_PASSWORD para uma senha segura

# Iniciar serviços
docker-compose up -d

# Executar migrações
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan storage:link
docker-compose exec app php artisan config:cache
docker-compose exec app php artisan route:cache
docker-compose exec app php artisan view:cache
```

### Passo 5: Configurar SSL e Domínio (15 min)
```bash
# Configurar Nginx
sudo rm /etc/nginx/sites-enabled/default
sudo ln -s /home/ubuntu/lucrativabet/nginx.conf /etc/nginx/sites-enabled/lucrativabet

# Gerar certificado SSL
sudo certbot --nginx -d lucrativa.bet -d www.lucrativa.bet

# Reiniciar Nginx
sudo systemctl restart nginx
```

## 🔒 Configurações de Segurança

### Firewall
```bash
# Liberar portas necessárias
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

### Monitoramento
```bash
# Monitorar logs
docker-compose logs -f app
docker-compose logs -f nginx

# Verificar status
docker-compose ps
docker-compose exec app php artisan api:health
```

## 📊 Monitoramento Pós-Deploy

### Health Checks
- `https://lucrativa.bet/api/health` - Principal
- `https://lucrativa.bet/health` - Load balancer

### Logs
```bash
# Aplicação
docker-compose logs -f app

# Nginx
sudo tail -f /var/log/nginx/access.log
sudo tail -f /var/log/nginx/error.log

# Banco de dados
docker-compose logs -f postgres
```

## 🚨 Backup e Manutenção

### Backup Automático
```bash
# Backup banco de dados
docker-compose exec postgres pg_dump -U lucrativabet lucrativabet > backup_$(date +%Y%m%d).sql

# Backup arquivos
tar -czf storage_$(date +%Y%m%d).tar.gz storage/
```

### Atualizações
```bash
# Atualizar aplicação
git pull origin main
docker-compose build app
docker-compose up -d
```

## 🎯 Testes de Validação

### 1. Health Check
```bash
curl https://lucrativa.bet/api/health
```

### 2. Frontend
Acessar `https://lucrativa.bet` e verificar:
- ✅ Homepage carrega
- ✅ Jogos listados
- ✅ Login funcional
- ✅ Cadastro funcional

### 3. Admin Panel
Acessar `https://lucrativa.bet/admin` e verificar:
- ✅ Login admin
- ✅ Dashboard carrega
- ✅ Usuários visíveis
- ✅ Jogos gerenciáveis

### 4. API Endpoints
```bash
# Health check
curl https://lucrativa.bet/api/health

# Lista jogos
curl https://lucrativa.bet/api/jogos/lista

# Promoções
curl https://lucrativa.bet/api/promocoes
```

## 🏆 Tempo Estimado Total
- **Setup**: 90 minutos
- **Testes**: 30 minutos
- **Total**: 2 horas

---

*🤖 Documentação gerada por Claude Code*  
*📅 Data: 11/09/2025*  
*✅ Status: Pronto para deploy*