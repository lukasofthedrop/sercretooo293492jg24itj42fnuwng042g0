#!/bin/bash

echo "🚀 Deploy para Render - LucrativaBet"
echo "======================================"

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Verificar se está no branch correto
CURRENT_BRANCH=$(git branch --show-current)
echo -e "${YELLOW}Branch atual: $CURRENT_BRANCH${NC}"

# Verificar status do git
echo -e "\n${YELLOW}📋 Verificando status do Git...${NC}"
git status --short

# Perguntar se deseja continuar
read -p "Deseja continuar com o deploy? (y/n) " -n 1 -r
echo
if [[ ! $REPLY =~ ^[Yy]$ ]]
then
    echo -e "${RED}Deploy cancelado.${NC}"
    exit 1
fi

# Adicionar todas as mudanças
echo -e "\n${YELLOW}📦 Preparando arquivos...${NC}"
git add -A

# Commit com mensagem descritiva
echo -e "\n${YELLOW}💾 Criando commit...${NC}"
COMMIT_MSG="🚀 Deploy Render - $(date '+%Y-%m-%d %H:%M:%S')"
git commit -m "$COMMIT_MSG" || echo -e "${YELLOW}Nenhuma mudança para commit${NC}"

# Push para o branch atual
echo -e "\n${YELLOW}📤 Enviando para GitHub...${NC}"
git push origin $CURRENT_BRANCH

# Criar/atualizar branch de produção se necessário
if [ "$CURRENT_BRANCH" != "main" ]; then
    echo -e "\n${YELLOW}🔄 Atualizando branch main...${NC}"
    git checkout main
    git merge $CURRENT_BRANCH --no-edit
    git push origin main
    git checkout $CURRENT_BRANCH
fi

echo -e "\n${GREEN}✅ Código enviado com sucesso!${NC}"
echo -e "\n${YELLOW}📝 Próximos passos:${NC}"
echo "1. Acesse https://dashboard.render.com"
echo "2. Crie um novo Web Service"
echo "3. Conecte o repositório GitHub"
echo "4. Selecione o branch 'main'"
echo "5. Use as seguintes configurações:"
echo "   - Build Command: ./render-build.sh"
echo "   - Start Command: Dockerfile já configura isso"
echo "   - Environment: Docker"
echo "   - Instance Type: Free"
echo ""
echo -e "${YELLOW}🔐 Variáveis de ambiente importantes:${NC}"
echo "   - APP_KEY (gerar nova)"
echo "   - Credenciais do banco PostgreSQL (criadas automaticamente)"
echo "   - PLAYFIVER_* (copiar do .env local)"
echo ""
echo -e "${GREEN}🎉 Deploy preparado com sucesso!${NC}"