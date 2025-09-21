/**
 * LUCRATIVABET DASHBOARD CHARTS
 * Sistema de gráficos dinâmicos com ApexCharts
 * Tema: Dark Premium com verde #22c55e
 */

// Configuração global do tema
const lucrativaTheme = {
    colors: {
        primary: '#22c55e',
        secondary: '#00ff41',
        danger: '#ef4444',
        warning: '#f59e0b',
        info: '#3b82f6',
        dark: '#1a1a1a',
        black: '#0a0a0a'
    },
    chart: {
        toolbar: {
            show: false
        },
        background: 'transparent',
        fontFamily: 'Inter, sans-serif'
    },
    theme: {
        mode: 'dark'
    }
};

// Classe principal para gerenciar os gráficos
class DashboardCharts {
    constructor() {
        this.charts = {};
        this.updateInterval = 15000; // 15 segundos
        this.apiEndpoint = '/api/admin/dashboard-metrics';
    }

    // Inicializar todos os gráficos
    init() {
        this.initDepositChart();
        this.initUsersChart();
        this.initGamesChart();
        this.initRevenueChart();
        this.startAutoUpdate();
    }

    // Gráfico de Depósitos (Área)
    initDepositChart() {
        const element = document.querySelector('#deposits-chart');
        if (!element) return;

        const options = {
            series: [{
                name: 'Depósitos',
                data: []
            }],
            chart: {
                ...lucrativaTheme.chart,
                type: 'area',
                height: 350,
                sparkline: {
                    enabled: false
                },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                    animateGradually: {
                        enabled: true,
                        delay: 150
                    }
                }
            },
            colors: [lucrativaTheme.colors.primary],
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.2,
                    stops: [0, 90, 100],
                    colorStops: [{
                        offset: 0,
                        color: lucrativaTheme.colors.primary,
                        opacity: 0.8
                    }, {
                        offset: 100,
                        color: lucrativaTheme.colors.primary,
                        opacity: 0.1
                    }]
                }
            },
            xaxis: {
                type: 'datetime',
                labels: {
                    style: {
                        colors: '#9ca3af'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#9ca3af'
                    },
                    formatter: (value) => 'R$ ' + value.toFixed(2)
                }
            },
            tooltip: {
                theme: 'dark',
                style: {
                    fontSize: '12px',
                    background: lucrativaTheme.colors.dark
                },
                y: {
                    formatter: (value) => 'R$ ' + value.toFixed(2)
                }
            },
            grid: {
                borderColor: 'rgba(34, 197, 94, 0.1)',
                strokeDashArray: 4
            }
        };

        this.charts.deposits = new ApexCharts(element, options);
        this.charts.deposits.render();
    }

    // Gráfico de Usuários (Linha com gradiente)
    initUsersChart() {
        const element = document.querySelector('#users-chart');
        if (!element) return;

        const options = {
            series: [{
                name: 'Novos Usuários',
                data: []
            }],
            chart: {
                ...lucrativaTheme.chart,
                type: 'line',
                height: 350
            },
            colors: [lucrativaTheme.colors.secondary],
            stroke: {
                width: 3,
                curve: 'smooth'
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'dark',
                    type: 'vertical',
                    shadeIntensity: 1,
                    gradientToColors: [lucrativaTheme.colors.primary],
                    opacityFrom: 0.8,
                    opacityTo: 0.2
                }
            },
            markers: {
                size: 4,
                colors: [lucrativaTheme.colors.secondary],
                strokeColors: lucrativaTheme.colors.black,
                strokeWidth: 2,
                hover: {
                    size: 7
                }
            },
            xaxis: {
                categories: [],
                labels: {
                    style: {
                        colors: '#9ca3af'
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'Usuários',
                    style: {
                        color: lucrativaTheme.colors.primary
                    }
                },
                labels: {
                    style: {
                        colors: '#9ca3af'
                    }
                }
            },
            tooltip: {
                theme: 'dark'
            },
            grid: {
                borderColor: 'rgba(34, 197, 94, 0.1)'
            }
        };

        this.charts.users = new ApexCharts(element, options);
        this.charts.users.render();
    }

    // Gráfico de Jogos (Donut interativo)
    initGamesChart() {
        const element = document.querySelector('#games-donut-chart');
        if (!element) return;

        const options = {
            series: [],
            chart: {
                ...lucrativaTheme.chart,
                type: 'donut',
                height: 380
            },
            colors: [
                lucrativaTheme.colors.primary,
                lucrativaTheme.colors.secondary,
                '#ff6b35',
                '#4dabf7',
                '#ffd43b'
            ],
            labels: [],
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            name: {
                                show: true,
                                fontSize: '16px',
                                fontWeight: 600,
                                color: '#ffffff'
                            },
                            value: {
                                show: true,
                                fontSize: '24px',
                                fontWeight: 900,
                                color: lucrativaTheme.colors.primary,
                                formatter: (val) => val + ' apostas'
                            },
                            total: {
                                show: true,
                                showAlways: true,
                                label: 'Total',
                                fontSize: '14px',
                                color: '#9ca3af',
                                formatter: (w) => {
                                    const total = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                    return total + ' apostas';
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                width: 2,
                colors: [lucrativaTheme.colors.black]
            },
            legend: {
                position: 'bottom',
                labels: {
                    colors: '#ffffff'
                },
                markers: {
                    width: 12,
                    height: 12,
                    radius: 3
                }
            },
            tooltip: {
                theme: 'dark',
                y: {
                    formatter: (val) => val + ' apostas'
                }
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: 300
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        this.charts.games = new ApexCharts(element, options);
        this.charts.games.render();
    }

    // Gráfico de Receita (Barras com gradiente)
    initRevenueChart() {
        const element = document.querySelector('#revenue-chart');
        if (!element) return;

        const options = {
            series: [{
                name: 'Receita',
                data: []
            }, {
                name: 'Lucro',
                data: []
            }],
            chart: {
                ...lucrativaTheme.chart,
                type: 'bar',
                height: 350
            },
            colors: [lucrativaTheme.colors.primary, lucrativaTheme.colors.secondary],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '55%',
                    borderRadius: 8,
                    dataLabels: {
                        position: 'top'
                    }
                }
            },
            dataLabels: {
                enabled: true,
                formatter: (val) => 'R$ ' + val,
                offsetY: -20,
                style: {
                    fontSize: '12px',
                    colors: [lucrativaTheme.colors.primary]
                }
            },
            stroke: {
                show: true,
                width: 2,
                colors: ['transparent']
            },
            xaxis: {
                categories: [],
                labels: {
                    style: {
                        colors: '#9ca3af'
                    }
                }
            },
            yaxis: {
                title: {
                    text: 'R$ (Reais)',
                    style: {
                        color: lucrativaTheme.colors.primary
                    }
                },
                labels: {
                    style: {
                        colors: '#9ca3af'
                    },
                    formatter: (val) => 'R$ ' + val
                }
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shade: 'light',
                    type: 'vertical',
                    shadeIntensity: 0.5,
                    gradientToColors: [lucrativaTheme.colors.dark],
                    inverseColors: false,
                    opacityFrom: 0.9,
                    opacityTo: 0.5
                }
            },
            tooltip: {
                theme: 'dark',
                y: {
                    formatter: (val) => 'R$ ' + val
                }
            },
            grid: {
                borderColor: 'rgba(34, 197, 94, 0.1)'
            }
        };

        this.charts.revenue = new ApexCharts(element, options);
        this.charts.revenue.render();
    }

    // Buscar dados da API
    async fetchData() {
        try {
            const response = await fetch(this.apiEndpoint, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) throw new Error('Erro ao buscar dados');

            const data = await response.json();
            this.updateCharts(data);
            this.animateCounters(data);
        } catch (error) {
            console.error('Erro ao atualizar dashboard:', error);
        }
    }

    // Atualizar todos os gráficos com novos dados
    updateCharts(data) {
        // Atualizar gráfico de depósitos
        if (this.charts.deposits && data.deposits) {
            this.charts.deposits.updateSeries([{
                data: data.deposits
            }]);
        }

        // Atualizar gráfico de usuários
        if (this.charts.users && data.users) {
            this.charts.users.updateOptions({
                xaxis: { categories: data.users.labels }
            });
            this.charts.users.updateSeries([{
                data: data.users.data
            }]);
        }

        // Atualizar gráfico de jogos
        if (this.charts.games && data.games) {
            this.charts.games.updateOptions({
                labels: data.games.labels
            });
            this.charts.games.updateSeries(data.games.data);
        }

        // Atualizar gráfico de receita
        if (this.charts.revenue && data.revenue) {
            this.charts.revenue.updateOptions({
                xaxis: { categories: data.revenue.labels }
            });
            this.charts.revenue.updateSeries([
                { data: data.revenue.receita },
                { data: data.revenue.lucro }
            ]);
        }
    }

    // Animar contadores
    animateCounters(data) {
        const counters = document.querySelectorAll('.counter-value');
        
        counters.forEach(counter => {
            const target = parseFloat(counter.getAttribute('data-target'));
            const duration = 2000;
            const increment = target / (duration / 16);
            let current = 0;

            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    counter.textContent = this.formatValue(current, counter.dataset.format);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = this.formatValue(target, counter.dataset.format);
                }
            };

            updateCounter();
        });
    }

    // Formatar valores
    formatValue(value, format = 'number') {
        switch(format) {
            case 'currency':
                return 'R$ ' + value.toFixed(2).replace('.', ',');
            case 'percentage':
                return value.toFixed(1) + '%';
            default:
                return Math.round(value).toLocaleString('pt-BR');
        }
    }

    // Iniciar atualização automática
    startAutoUpdate() {
        // Buscar dados iniciais
        this.fetchData();

        // Configurar intervalo de atualização
        setInterval(() => {
            this.fetchData();
            this.showUpdateNotification();
        }, this.updateInterval);
    }

    // Notificação de atualização
    showUpdateNotification() {
        const notification = document.createElement('div');
        notification.className = 'update-notification';
        notification.innerHTML = `
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-green-400 animate-spin" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span>Dashboard atualizado</span>
            </div>
        `;
        notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: linear-gradient(135deg, #1a1a1a, #0f0f0f);
            color: #22c55e;
            padding: 12px 20px;
            border-radius: 8px;
            border: 1px solid #22c55e;
            box-shadow: 0 4px 20px rgba(34, 197, 94, 0.3);
            z-index: 9999;
            animation: slideIn 0.3s ease-out;
        `;

        document.body.appendChild(notification);
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    // Criar mini gráficos sparkline
    createSparkline(element, data, color = lucrativaTheme.colors.primary) {
        const options = {
            series: [{
                data: data
            }],
            chart: {
                type: 'line',
                height: 60,
                sparkline: { enabled: true },
                animations: {
                    enabled: true,
                    easing: 'linear',
                    dynamicAnimation: {
                        speed: 1000
                    }
                }
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            colors: [color],
            tooltip: {
                enabled: false
            }
        };

        const chart = new ApexCharts(element, options);
        chart.render();
        return chart;
    }
}

// CSS para animações
const style = document.createElement('style');
style.textContent = `
@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.update-notification {
    font-family: 'Inter', sans-serif;
    font-size: 14px;
    font-weight: 600;
}
`;
document.head.appendChild(style);

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    // Verificar se ApexCharts está disponível
    if (typeof ApexCharts !== 'undefined') {
        const dashboard = new DashboardCharts();
        dashboard.init();
        window.dashboardCharts = dashboard; // Expor globalmente para debug
    } else {
        console.log('ApexCharts não encontrado, carregando via CDN...');
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/apexcharts';
        script.onload = () => {
            const dashboard = new DashboardCharts();
            dashboard.init();
            window.dashboardCharts = dashboard;
        };
        document.head.appendChild(script);
    }
});

// Suporte para Livewire
document.addEventListener('livewire:navigated', () => {
    if (window.dashboardCharts) {
        window.dashboardCharts.init();
    }
});