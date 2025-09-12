const { chromium } = require('playwright');

(async () => {
    console.log('🚀 Iniciando automação de deploy no Render...');
    
    // Iniciar navegador
    const browser = await chromium.launch({ 
        headless: false, // Mudar para true se quiser modo headless
        slowMo: 1000 // Reduzir velocidade para visualização
    });
    
    const context = await browser.newContext({
        viewport: { width: 1920, height: 1080 }
    });
    
    const page = await context.newPage();
    
    try {
        console.log('📂 Navegando para dashboard do Render...');
        await page.goto('https://dashboard.render.com');
        
        // Esperar carregar a página
        await page.waitForLoadState('networkidle');
        console.log('✅ Página do Render carregada');
        
        // Verificar se já está logado ou precisa fazer login
        const loginButton = await page.$('text=Login with GitHub');
        if (loginButton) {
            console.log('🔐 Precisa fazer login com GitHub...');
            await loginButton.click();
            
            // Esperar página de login do GitHub
            await page.waitForURL('https://github.com/login');
            console.log('✅ Página de login do GitHub carregada');
            
            // Preencher credenciais
            await page.fill('input[name="login"]', 'rk.impulsodigital@gmail.com');
            await page.fill('input[name="password"]', process.env.GITHUB_PASSWORD || 'SUA_SENHA_AQUI');
            
            // Fazer login
            await page.click('input[name="commit"]');
            
            // Esperar redirecionamento de volta ao Render
            await page.waitForURL('https://dashboard.render.com');
            console.log('✅ Login realizado com sucesso!');
        } else {
            console.log('✅ Já está logado no Render');
        }
        
        // Esperar dashboard carregar completamente
        await page.waitForLoadState('networkidle');
        
        console.log('➕ Criando novo Web Service...');
        
        // Clicar no botão "New +"
        await page.click('[aria-label="New"]'); // ou outro seletor apropriado
        
        // Esperar menu dropdown
        await page.waitForTimeout(2000);
        
        // Clicar em "Web Service"
        await page.click('text=Web Service');
        
        console.log('📚 Aguardando página de criar serviço...');
        
        // Esperar página de criação de serviço
        await page.waitForURL(/\/new/);
        await page.waitForLoadState('networkidle');
        
        console.log('🔗 Conectando ao repositório...');
        
        // Procurar e selecionar o repositório correto
        await page.fill('input[placeholder*="Search"]', 'lukasofthedrop/sercretooo293492jg24itj42fnuwng042g0');
        await page.waitForTimeout(2000);
        
        // Clicar no repositório correto
        await page.click('text=lukasofthedrop/sercretooo293492jg24itj42fnuwng042g0.git');
        
        console.log('🌿 Selecionando branch render-clean...');
        
        // Esperar a seção de branch
        await page.waitForSelector('select[name="branch"]');
        await page.selectOption('select[name="branch"]', 'render-clean');
        
        console.log('⏳ Aguardando detecção do render.yaml...');
        
        // Esperar o Render detectar a configuração automaticamente
        await page.waitForSelector('text=render.yaml detected', { timeout: 30000 });
        console.log('✅ render.yaml detectado automaticamente!');
        
        console.log('🚀 Criando Web Service...');
        
        // Clicar em "Create Web Service"
        await page.click('button:has-text("Create Web Service")');
        
        console.log('📊 Aguardando início do deploy...');
        
        // Esperar redirecionamento para a página do serviço
        await page.waitForURL(/\/services\/.+/);
        await page.waitForLoadState('networkidle');
        
        console.log('🔍 Monitorando status do deploy...');
        
        // Monitorar o status do deploy
        let deployStatus = '';
        let attempts = 0;
        const maxAttempts = 60; // 10 minutos máximo
        
        while (attempts < maxAttempts) {
            attempts++;
            
            // Recarregar a página para obter status atualizado
            await page.reload();
            await page.waitForLoadState('networkidle');
            
            // Verificar status do deploy
            const statusElement = await page.$('[data-testid="deploy-status"], .deploy-status, text=Live, text=Building, text=Failed');
            if (statusElement) {
                deployStatus = await statusElement.textContent();
                console.log(`📈 Status do deploy: ${deployStatus}`);
            }
            
            // Verificar se o deploy foi concluído com sucesso
            if (deployStatus && deployStatus.includes('Live')) {
                console.log('🎉 DEPLOY CONCLUÍDO COM SUCESSO!');
                console.log('✅ Sistema LucrativaBet está ONLINE no Render!');
                break;
            }
            
            // Verificar se houve falha
            if (deployStatus && deployStatus.includes('Failed')) {
                console.log('❌ DEPLOY FALHOU!');
                console.log('🔍 Verificando logs de erro...');
                
                // Tentar encontrar e mostrar logs de erro
                try {
                    await page.click('text=Logs');
                    await page.waitForLoadState('networkidle');
                    const logs = await page.textContent('.logs-container, pre, code');
                    console.log('📝 Logs de erro:', logs);
                } catch (e) {
                    console.log('Não foi possível obter os logs automaticamente');
                }
                break;
            }
            
            // Esperar 10 segundos antes da próxima verificação
            await page.waitForTimeout(10000);
        }
        
        if (attempts >= maxAttempts) {
            console.log('⏰ Tempo limite excedido monitorando o deploy');
            console.log('🔍 Por favor, verifique manualmente o status no dashboard do Render');
        }
        
        // Capturar screenshot final
        await page.screenshot({ path: '/Users/rkripto/Downloads/lucrativabet/deploy-final-status.png' });
        console.log('📸 Screenshot final salvo');
        
    } catch (error) {
        console.error('❌ ERRO durante o deploy:', error);
        
        // Capturar screenshot do erro
        await page.screenshot({ path: '/Users/rkripto/Downloads/lucrativabet/deploy-error.png' });
        console.log('📸 Screenshot de erro salvo');
        
    } finally {
        // Fechar navegador
        await browser.close();
        console.log('🔚 Navegador fechado');
    }
})();