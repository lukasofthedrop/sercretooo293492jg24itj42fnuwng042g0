<x-filament-widgets::widget>
    <x-filament::section>
        <!-- Gráficos Profissionais LucrativaBet - Responsivo com Livewire -->
        
        <div wire:ignore class="dashboard-metrics-grid grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 mt-4 lg:mt-6 px-2 sm:px-0">
            <!-- Gráfico de Depósitos Profissional -->
            <div class="metric-card bg-gradient-to-br from-slate-900 to-slate-800 rounded-lg lg:rounded-xl p-4 lg:p-6 shadow-xl border border-slate-700/50 hover:border-green-500/30 transition-all">
                <div class="flex items-center justify-between mb-3 lg:mb-4">
                    <h3 class="text-white font-bold text-base lg:text-lg flex items-center gap-1 lg:gap-2">
                        <span class="text-xl lg:text-2xl">📈</span>
                        <span class="hidden sm:inline">DEPÓSITOS</span>
                        <span class="sm:hidden">DEP.</span>
                        <span class="text-xs lg:text-sm text-slate-400 font-normal hidden md:inline">(HOJE)</span>
                    </h3>
                    <div class="bg-green-500/20 text-green-400 px-2 lg:px-3 py-1 rounded-full text-xs font-medium animate-pulse">
                        <span class="hidden sm:inline">AO VIVO</span>
                        <span class="sm:hidden">LIVE</span>
                    </div>
                </div>
                <div id="deposits-chart" class="chart-container responsive-chart" style="min-height: 250px; height: 100%; max-height: 400px;">
                    <!-- Container para gráfico de depósitos -->
                </div>
            </div>
            
            <!-- Gráfico de Novos Usuários Profissional -->
            <div class="metric-card bg-gradient-to-br from-slate-900 to-slate-800 rounded-lg lg:rounded-xl p-4 lg:p-6 shadow-xl border border-slate-700/50 hover:border-blue-500/30 transition-all">
                <div class="flex items-center justify-between mb-3 lg:mb-4">
                    <h3 class="text-white font-bold text-base lg:text-lg flex items-center gap-1 lg:gap-2">
                        <span class="text-xl lg:text-2xl">👥</span>
                        <span class="hidden sm:inline">NOVOS USUÁRIOS</span>
                        <span class="sm:hidden">USUÁRIOS</span>
                        <span class="text-xs lg:text-sm text-slate-400 font-normal hidden md:inline">(HOJE)</span>
                    </h3>
                    <div class="bg-blue-500/20 text-blue-400 px-2 lg:px-3 py-1 rounded-full text-xs font-medium animate-pulse">
                        <span class="hidden sm:inline">AO VIVO</span>
                        <span class="sm:hidden">LIVE</span>
                    </div>
                </div>
                <div id="users-chart" class="chart-container responsive-chart" style="min-height: 250px; height: 100%; max-height: 400px;">
                    <!-- Container para gráfico de usuários -->
                </div>
            </div>
        </div>
        
        <style>
        /* Base styles para containers */
        .chart-container {
            position: relative;
            width: 100%;
            overflow: hidden;
        }
        
        /* Animação sutil no topo dos gráficos */
        .chart-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, 
                transparent, 
                #22c55e 20%, 
                #22c55e 80%, 
                transparent);
            opacity: 0.5;
            animation: slideAnimation 3s ease-in-out infinite;
            z-index: 10;
        }
        
        @keyframes slideAnimation {
            0%, 100% { transform: translateX(-100%); }
            50% { transform: translateX(100%); }
        }
        
        /* Estilos para tooltips ApexCharts */
        .apexcharts-tooltip {
            background: rgba(15, 23, 42, 0.95) !important;
            border: 1px solid rgba(34, 197, 94, 0.3) !important;
            border-radius: 8px !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5) !important;
            font-size: 12px !important;
        }
        
        .apexcharts-tooltip-title {
            background: rgba(34, 197, 94, 0.1) !important;
            border-bottom: 1px solid rgba(34, 197, 94, 0.2) !important;
            font-weight: bold !important;
            padding: 8px 10px !important;
        }
        
        /* Menu de ferramentas */
        .apexcharts-menu {
            background: rgba(15, 23, 42, 0.95) !important;
            border: 1px solid rgba(34, 197, 94, 0.3) !important;
        }
        
        .apexcharts-menu-item:hover {
            background: rgba(34, 197, 94, 0.2) !important;
        }
        
        /* Responsividade Mobile */
        @media (max-width: 768px) {
            .dashboard-metrics-grid {
                padding: 0 !important;
            }
            
            .metric-card {
                border-radius: 0.5rem !important;
                margin-bottom: 1rem;
            }
            
            /* Ajustar labels dos eixos em mobile */
            .apexcharts-xaxis-label {
                font-size: 10px !important;
            }
            
            .apexcharts-yaxis-label {
                font-size: 10px !important;
            }
            
            /* Reduzir padding do tooltip em mobile */
            .apexcharts-tooltip {
                padding: 4px !important;
                font-size: 11px !important;
            }
            
            /* Ocultar grid lines em mobile para limpeza visual */
            .apexcharts-gridlines-horizontal {
                stroke-dasharray: 2 !important;
                opacity: 0.3 !important;
            }
        }
        
        /* Tablet styles */
        @media (min-width: 769px) and (max-width: 1024px) {
            .dashboard-metrics-grid {
                gap: 1rem !important;
            }
            
            .metric-card {
                padding: 1.25rem !important;
            }
        }
        
        /* Desktop large screens */
        @media (min-width: 1440px) {
            .dashboard-metrics-grid {
                max-width: 1400px;
                margin: 0 auto;
            }
        }
        
        /* Dark mode optimizations */
        @media (prefers-color-scheme: dark) {
            .metric-card {
                background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
            }
        }
        
        /* Performance optimization for animations */
        @media (prefers-reduced-motion: reduce) {
            .chart-container::before {
                animation: none !important;
            }
            
            .animate-pulse {
                animation: none !important;
            }
        }
        
        /* Loading state */
        .chart-container.loading {
            min-height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(34, 197, 94, 0.05);
        }
        
        .chart-container.loading::after {
            content: 'Carregando dados...';
            color: #22c55e;
            font-size: 14px;
            animation: pulse 1.5s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 1; }
        }
        </style>
        
        @script
        <script>
        console.log('🚀 Script Livewire iniciado - LucrativaBet Charts');
        
        // Gerenciamento global de charts
        window.LucrativaCharts = window.LucrativaCharts || {
            deposits: null,
            users: null,
            initialized: false
        };
        
        async function initLucrativaCharts() {
            // Prevenir inicialização duplicada
            if (window.LucrativaCharts.initialized) {
                console.log('⚠️ Charts já inicializados');
                return;
            }
            
            console.log('🔄 Iniciando renderização dos gráficos...');
            
            // Verificar se ApexCharts está disponível
            if (typeof ApexCharts === 'undefined') {
                console.log('⏳ ApexCharts não disponível, aguardando...');
                setTimeout(initLucrativaCharts, 100);
                return;
            }
            
            // Verificar containers
            const depositsContainer = document.querySelector('#deposits-chart');
            const usersContainer = document.querySelector('#users-chart');
            
            if (!depositsContainer || !usersContainer) {
                console.log('⏳ Containers não encontrados, aguardando...');
                setTimeout(initLucrativaCharts, 100);
                return;
            }
            
            // Destruir charts anteriores se existirem
            if (window.LucrativaCharts.deposits) {
                window.LucrativaCharts.deposits.destroy();
                window.LucrativaCharts.deposits = null;
            }
            if (window.LucrativaCharts.users) {
                window.LucrativaCharts.users.destroy();
                window.LucrativaCharts.users = null;
            }
            
            // Limpar containers
            depositsContainer.innerHTML = '';
            usersContainer.innerHTML = '';
            
            // Buscar dados reais da API
            let depositsData = [];
            let depositsCategories = [];
            let usersData = [];
            let usersCategories = [];
            
            try {
                console.log('📡 Buscando dados da API...');
                const response = await fetch('/api/admin/dashboard-metrics?period=today');
                const apiData = await response.json();
                
                // Processar dados de depósitos
                if (apiData.deposits && apiData.deposits.length > 0) {
                    depositsData = apiData.deposits.map(item => item.y || 0);
                    depositsCategories = apiData.deposits.map(item => {
                        const date = new Date(item.x);
                        return date.getHours() + 'h';
                    });
                } else {
                    // Se não houver dados, criar array vazio para 24 horas
                    for (let i = 0; i < 24; i++) {
                        depositsData.push(0);
                        depositsCategories.push(i + 'h');
                    }
                }
                
                // Processar dados de usuários
                if (apiData.users && apiData.users.data) {
                    usersData = apiData.users.data;
                    usersCategories = apiData.users.labels || [];
                } else {
                    // Se não houver dados, criar array vazio para 24 horas
                    for (let i = 0; i < 24; i++) {
                        usersData.push(0);
                        usersCategories.push(i + 'h');
                    }
                }
                
                console.log('✅ Dados da API recebidos:', { depositsData, usersData });
                
            } catch (error) {
                console.error('❌ Erro ao buscar dados da API:', error);
                // Usar dados vazios em caso de erro
                for (let i = 0; i < 24; i++) {
                    depositsData.push(0);
                    depositsCategories.push(i + 'h');
                    usersData.push(0);
                    usersCategories.push(i + 'h');
                }
            }
            
            // Configuração tema
            const chartTheme = {
                mode: 'dark',
                palette: 'palette1',
                monochrome: {
                    enabled: false,
                    color: '#22c55e',
                    shadeTo: 'dark',
                    shadeIntensity: 0.65
                }
            };
            
            // Gráfico de Depósitos com dados reais
            const depositsOptions = {
                series: [{
                    name: 'Depósitos',
                    data: depositsData
                }],
                chart: {
                    type: 'area',
                    height: 300,
                    background: 'transparent',
                    toolbar: { show: false },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                colors: ['#22c55e'],
                dataLabels: { enabled: false },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.6,
                        opacityTo: 0.1,
                        stops: [0, 100]
                    }
                },
                xaxis: {
                    categories: depositsCategories,
                    labels: {
                        style: {
                            colors: '#94a3b8',
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#94a3b8',
                            fontSize: '12px'
                        },
                        formatter: function(val) {
                            return 'R$ ' + val.toLocaleString('pt-BR');
                        }
                    }
                },
                grid: {
                    borderColor: '#334155',
                    strokeDashArray: 4,
                    xaxis: { lines: { show: false } },
                    yaxis: { lines: { show: true } }
                },
                theme: chartTheme,
                tooltip: {
                    theme: 'dark',
                    y: {
                        formatter: function(val) {
                            return 'R$ ' + val.toLocaleString('pt-BR');
                        }
                    }
                },
                noData: {
                    text: 'Sem dados de depósitos para hoje',
                    align: 'center',
                    verticalAlign: 'middle',
                    style: {
                        color: '#94a3b8',
                        fontSize: '14px'
                    }
                }
            };
            
            // Gráfico de Usuários com dados reais
            const usersOptions = {
                series: [{
                    name: 'Novos Usuários',
                    data: usersData
                }],
                chart: {
                    type: 'bar',
                    height: 300,
                    background: 'transparent',
                    toolbar: { show: false },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800
                    }
                },
                colors: ['#3b82f6'],
                plotOptions: {
                    bar: {
                        borderRadius: 6,
                        columnWidth: '60%',
                        distributed: false
                    }
                },
                dataLabels: { enabled: false },
                xaxis: {
                    categories: usersCategories,
                    labels: {
                        style: {
                            colors: '#94a3b8',
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#94a3b8',
                            fontSize: '12px'
                        }
                    }
                },
                grid: {
                    borderColor: '#334155',
                    strokeDashArray: 4,
                    xaxis: { lines: { show: false } },
                    yaxis: { lines: { show: true } }
                },
                theme: chartTheme,
                tooltip: {
                    theme: 'dark'
                },
                noData: {
                    text: 'Sem dados de usuários para hoje',
                    align: 'center',
                    verticalAlign: 'middle',
                    style: {
                        color: '#94a3b8',
                        fontSize: '14px'
                    }
                }
            };
            
            // Renderizar gráficos
            try {
                window.LucrativaCharts.deposits = new ApexCharts(depositsContainer, depositsOptions);
                window.LucrativaCharts.deposits.render();
                console.log('✅ Gráfico de depósitos renderizado com dados reais');
                
                window.LucrativaCharts.users = new ApexCharts(usersContainer, usersOptions);
                window.LucrativaCharts.users.render();
                console.log('✅ Gráfico de usuários renderizado com dados reais');
                
                // Marcar como inicializado
                window.LucrativaCharts.initialized = true;
                
                // Atualizar a cada 30 segundos
                setTimeout(() => {
                    window.LucrativaCharts.initialized = false;
                    initLucrativaCharts();
                }, 30000);
                
            } catch (error) {
                console.error('❌ Erro ao renderizar gráficos:', error);
                window.LucrativaCharts.initialized = false;
            }
        }
        
        // Executar inicialização
        setTimeout(initLucrativaCharts, 100);
        
        // Reinicializar em navegação Livewire
        document.addEventListener('livewire:navigated', function() {
            console.log('🔄 Livewire navigated - reinicializando...');
            window.LucrativaCharts.initialized = false;
            setTimeout(initLucrativaCharts, 100);
        });
        
        // Reinicializar quando componente for atualizado
        Livewire.hook('morph.updated', ({ el, component }) => {
            if (el.querySelector && (el.querySelector('#deposits-chart') || el.querySelector('#users-chart'))) {
                console.log('🔄 Componente atualizado - reinicializando...');
                window.LucrativaCharts.initialized = false;
                setTimeout(initLucrativaCharts, 100);
            }
        });
        </script>
        @endscript
    </x-filament::section>
</x-filament-widgets::widget>