<!-- TOP 5 JOGOS MAIS POPULARES - ApexCharts Professional -->
<div class="apex-games-container bg-gradient-to-br from-slate-900 to-slate-800 rounded-lg lg:rounded-xl p-4 md:p-5 lg:p-6 shadow-xl border border-slate-700/50 hover:border-green-500/30 transition-all">
    
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 lg:mb-6 gap-3">
        <div class="flex items-center gap-2 lg:gap-3">
            <div class="text-2xl md:text-3xl lg:text-4xl">🎮</div>
            <div>
                <h3 class="text-white font-bold text-base md:text-lg lg:text-xl">TOP 5 JOGOS MAIS POPULARES</h3>
                <p class="text-slate-400 text-xs md:text-sm lg:text-base">Análise de Performance em Tempo Real</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <div class="bg-green-500/20 text-green-400 px-2 lg:px-3 py-1 rounded-full text-xs font-medium animate-pulse">
                <span class="hidden sm:inline">AO VIVO</span>
                <span class="sm:hidden">LIVE</span>
            </div>
            @if(count($chartData['labels']) > 0)
            <button onclick="refreshTop5Games()" class="bg-slate-700 hover:bg-slate-600 text-white p-1.5 lg:p-2 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </button>
            @endif
        </div>
    </div>

    @if(count($chartData['labels']) > 0)
        <!-- KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4 lg:gap-5 mb-4 md:mb-5 lg:mb-6">
            <div class="bg-gradient-to-br from-blue-600/10 to-blue-500/5 border border-blue-500/20 rounded-lg p-3 md:p-4 lg:p-5">
                <div class="flex flex-row items-center justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <div class="text-slate-400 text-[10px] sm:text-xs md:text-sm uppercase font-medium truncate">Total Apostas</div>
                        <div class="text-blue-400 text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold">{{ number_format(array_sum($chartData['data']), 0, ',', '.') }}</div>
                    </div>
                    <div class="text-xl sm:text-2xl md:text-3xl lg:text-4xl opacity-50 flex-shrink-0">📊</div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-green-600/10 to-green-500/5 border border-green-500/20 rounded-lg p-3 md:p-4 lg:p-5">
                <div class="flex flex-row items-center justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <div class="text-slate-400 text-[10px] sm:text-xs md:text-sm uppercase font-medium truncate">Receita Total</div>
                        <div class="text-green-400 text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold">R$ {{ number_format(array_sum($chartData['amounts']), 0, ',', '.') }}</div>
                    </div>
                    <div class="text-xl sm:text-2xl md:text-3xl lg:text-4xl opacity-50 flex-shrink-0">💰</div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-purple-600/10 to-purple-500/5 border border-purple-500/20 rounded-lg p-3 md:p-4 lg:p-5">
                <div class="flex flex-row items-center justify-between gap-2">
                    <div class="flex-1 min-w-0">
                        <div class="text-slate-400 text-[10px] sm:text-xs md:text-sm uppercase font-medium truncate">Média/Aposta</div>
                        <div class="text-purple-400 text-lg sm:text-xl md:text-2xl lg:text-3xl font-bold">R$ {{ number_format(array_sum($chartData['amounts']) / max(array_sum($chartData['data']), 1), 0, ',', '.') }}</div>
                    </div>
                    <div class="text-xl sm:text-2xl md:text-3xl lg:text-4xl opacity-50 flex-shrink-0">💎</div>
                </div>
            </div>
        </div>

        <!-- Main Chart Container -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-5 lg:gap-6 mb-6 lg:mb-8">
            <!-- Bar Chart -->
            <div class="bg-slate-800/50 rounded-lg p-4 md:p-5 lg:p-6 border border-slate-700 hover:border-green-500/30 transition-all">
                <h4 class="text-white font-bold text-base md:text-lg lg:text-xl mb-3 md:mb-4 lg:mb-5 flex items-center gap-2 pb-2 md:pb-3 border-b border-slate-700/50">
                    <span class="text-xl md:text-2xl lg:text-3xl">📈</span> 
                    <span class="text-base md:text-lg lg:text-xl font-bold uppercase tracking-wider">Ranking por Popularidade</span>
                </h4>
                <div id="top5-games-bar-chart" class="chart-container" style="height: 300px; overflow: hidden;">
                    <!-- ApexCharts Bar Chart -->
                </div>
            </div>
            
            <!-- Radial Chart -->
            <div class="bg-slate-800/50 rounded-lg p-4 md:p-5 lg:p-6 border border-slate-700 hover:border-blue-500/30 transition-all">
                <h4 class="text-white font-bold text-base md:text-lg lg:text-xl mb-3 md:mb-4 lg:mb-5 flex items-center gap-2 pb-2 md:pb-3 border-b border-slate-700/50">
                    <span class="text-xl md:text-2xl lg:text-3xl">🎯</span> 
                    <span class="text-base md:text-lg lg:text-xl font-bold uppercase tracking-wider">Performance Individual</span>
                </h4>
                <div id="top5-games-radial-chart" class="chart-container" style="height: 300px; overflow: hidden;">
                    <!-- ApexCharts Radial Chart -->
                </div>
            </div>
        </div>

        <!-- Detailed Ranking List -->
        <div class="mt-6 md:mt-7 lg:mt-8">
            <div class="bg-slate-900/30 rounded-lg p-4 md:p-5 lg:p-6 mb-3 md:mb-4 lg:mb-5 border border-slate-700">
                <h4 class="text-white font-bold text-base md:text-lg lg:text-xl flex items-center gap-2 pb-2 md:pb-3 border-b border-slate-700/50">
                    <span class="text-xl md:text-2xl lg:text-3xl">🏆</span> 
                    <span class="text-base md:text-lg lg:text-xl font-bold uppercase tracking-wider">Detalhamento Completo</span>
                </h4>
            </div>
            <div class="space-y-3 lg:space-y-4">
            
            @foreach($chartData['labels'] as $index => $game)
                @php
                    $percentage = ($chartData['data'][$index] / max(array_sum($chartData['data']), 1)) * 100;
                    $revenue = $chartData['amounts'][$index] ?? 0;
                @endphp
                
                <div class="game-ranking-item bg-gradient-to-r from-slate-800/50 to-transparent rounded-lg p-3 lg:p-4 border border-slate-700/50 hover:border-green-500/30 transition-all">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <!-- Position Badge -->
                            <div class="position-badge w-8 h-8 lg:w-10 lg:h-10 rounded-full flex items-center justify-center font-bold text-sm lg:text-base text-white shadow-lg flex-shrink-0"
                                 style="background: linear-gradient(135deg, {{ $chartData['colors'][$index] }}, {{ $chartData['colors'][$index] }}DD);">
                                {{ $index + 1 }}°
                            </div>
                            
                            <!-- Game Info -->
                            <div class="min-w-0 flex-1">
                                <div class="text-white font-semibold text-sm lg:text-base truncate">{{ $game }}</div>
                                <div class="text-xs mt-0.5">
                                    @if($index == 0) 
                                        <span class="text-yellow-400">👑 Líder Absoluto</span>
                                    @elseif($index == 1) 
                                        <span class="text-slate-300">🥈 Vice-Líder</span>
                                    @elseif($index == 2) 
                                        <span class="text-orange-400">🥉 3º Lugar</span>
                                    @else 
                                        <span class="text-slate-400">⭐ Top {{ $index + 1 }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Stats -->
                        <div class="flex items-center gap-3 lg:gap-4 ml-11 sm:ml-0">
                            <div class="text-right">
                                <div class="text-slate-400 text-[10px] lg:text-xs uppercase">Apostas</div>
                                <div class="text-white font-bold text-sm lg:text-base">{{ number_format($chartData['data'][$index], 0, ',', '.') }}</div>
                            </div>
                            
                            <div class="text-right">
                                <div class="text-slate-400 text-[10px] lg:text-xs uppercase">Receita</div>
                                <div class="text-green-400 font-bold text-sm lg:text-base">R$ {{ number_format($revenue, 0, ',', '.') }}</div>
                            </div>
                            
                            <div class="text-right min-w-[50px] lg:min-w-[60px]">
                                <div class="text-slate-400 text-[10px] lg:text-xs uppercase">Share</div>
                                <div class="text-blue-400 font-bold text-sm lg:text-base">{{ number_format($percentage, 1) }}%</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="mt-2 lg:mt-3 w-full bg-slate-700 rounded-full h-1.5 lg:h-2 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-1000 ease-out progress-bar"
                             style="width: {{ $percentage }}%; background: linear-gradient(90deg, {{ $chartData['colors'][$index] }}, {{ $chartData['colors'][$index] }}99);">
                        </div>
                    </div>
                </div>
            @endforeach
            </div>
        </div>

        <!-- Footer Stats -->
        <div class="mt-6 bg-gradient-to-r from-green-600/10 to-blue-600/10 rounded-lg p-3 sm:p-4 border border-green-500/20">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 sm:gap-4 text-xs sm:text-sm">
                <div class="flex items-center gap-2 text-slate-300">
                    <span class="text-base sm:text-lg">💡</span>
                    <span class="text-xs sm:text-sm">O jogo líder representa <strong class="text-green-400">{{ number_format((max($chartData['data']) / max(array_sum($chartData['data']), 1)) * 100, 1) }}%</strong> do volume total</span>
                </div>
                <div class="flex items-center gap-3 sm:gap-4 text-[10px] sm:text-xs text-slate-400">
                    <span>🔄 Cache: 15min</span>
                    <span>📊 Precisão: 99.9%</span>
                </div>
            </div>
        </div>

    @else
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-slate-800 rounded-full mb-4">
                <span class="text-4xl opacity-50">🎮</span>
            </div>
            <h4 class="text-white font-semibold text-lg mb-2">Aguardando Dados</h4>
            <p class="text-slate-400 text-sm max-w-md mx-auto">
                Os gráficos serão exibidos assim que os primeiros jogos receberem apostas.
            </p>
            <div class="mt-6 inline-flex items-center gap-2 bg-slate-800 rounded-lg px-4 py-2">
                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                <span class="text-green-400 text-sm font-medium">Sistema Monitorando</span>
            </div>
        </div>
    @endif
</div>

<style>
/* Custom styles for Top 5 Games Widget */
.apex-games-container {
    transition: all 0.3s ease;
}

.chart-container {
    position: relative;
    width: 100%;
    height: 300px !important;
    overflow: hidden !important;
}

.game-ranking-item {
    position: relative;
    overflow: hidden;
}

.game-ranking-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(34, 197, 94, 0.1), transparent);
    transition: left 0.6s ease;
}

.game-ranking-item:hover::before {
    left: 100%;
}

.position-badge {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    transition: transform 0.3s ease;
}

.game-ranking-item:hover .position-badge {
    transform: scale(1.1) rotate(5deg);
}

.progress-bar {
    animation: progressFill 1.5s ease-out;
}

@keyframes progressFill {
    from { width: 0; }
}

/* ApexCharts custom theme */
.apexcharts-tooltip {
    background: rgba(15, 23, 42, 0.95) !important;
    border: 1px solid rgba(34, 197, 94, 0.3) !important;
    border-radius: 8px !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5) !important;
}

.apexcharts-tooltip-title {
    background: rgba(34, 197, 94, 0.1) !important;
    border-bottom: 1px solid rgba(34, 197, 94, 0.2) !important;
    font-weight: bold !important;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .apex-games-container {
        padding: 1rem !important;
        margin: 0.5rem 0;
    }
    
    .game-ranking-item {
        padding: 0.75rem;
    }
    
    .position-badge {
        width: 1.75rem;
        height: 1.75rem;
        font-size: 0.7rem;
    }
    
    /* Stack stats vertically on very small screens */
    @media (max-width: 400px) {
        .game-ranking-item .flex-row {
            flex-direction: column;
            align-items: flex-start;
        }
    }
}

@media (min-width: 641px) and (max-width: 768px) {
    .game-ranking-item {
        padding: 1rem;
    }
    
    .position-badge {
        width: 2rem;
        height: 2rem;
        font-size: 0.75rem;
    }
}

@media (min-width: 769px) and (max-width: 1024px) {
    .apex-games-container {
        padding: 1.5rem;
    }
}
</style>

@script
<script>
console.log('🎮 Iniciando Top 5 Games ApexCharts Widget');

// Global chart instances
window.Top5GamesCharts = window.Top5GamesCharts || {
    barChart: null,
    radialChart: null,
    initialized: false
};

function initTop5GamesCharts() {
    @if(count($chartData['labels']) == 0)
        console.log('⚠️ Sem dados para exibir nos gráficos Top 5');
        return;
    @endif
    
    // Check if ApexCharts is available
    if (typeof ApexCharts === 'undefined') {
        console.log('⏳ Aguardando ApexCharts...');
        setTimeout(initTop5GamesCharts, 100);
        return;
    }
    
    // Prevent duplicate initialization
    if (window.Top5GamesCharts.initialized) {
        console.log('✅ Top 5 Games Charts já inicializados');
        return;
    }
    
    // Prepare data
    const gameLabels = {!! json_encode($chartData['labels']) !!};
    const gameData = {!! json_encode($chartData['data']) !!};
    const gameAmounts = {!! json_encode($chartData['amounts'] ?? []) !!};
    const gameColors = {!! json_encode($chartData['colors']) !!};
    
    // Destroy existing charts
    if (window.Top5GamesCharts.barChart) {
        window.Top5GamesCharts.barChart.destroy();
        window.Top5GamesCharts.barChart = null;
    }
    if (window.Top5GamesCharts.radialChart) {
        window.Top5GamesCharts.radialChart.destroy();
        window.Top5GamesCharts.radialChart = null;
    }
    
    // Chart theme configuration
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
    
    // 1. Horizontal Bar Chart
    const barContainer = document.querySelector('#top5-games-bar-chart');
    if (barContainer) {
        barContainer.innerHTML = '';
        
        const barOptions = {
            series: [{
                name: 'Apostas',
                data: gameData.slice().reverse() // Reverse for better visualization
            }],
            chart: {
                type: 'bar',
                height: 280,
                background: 'transparent',
                toolbar: { show: false },
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                    animateGradually: {
                        enabled: true,
                        delay: 150
                    },
                    dynamicAnimation: {
                        enabled: true,
                        speed: 350
                    }
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    horizontal: true,
                    distributed: true,
                    dataLabels: {
                        position: 'right'
                    }
                }
            },
            colors: gameColors.slice().reverse(),
            dataLabels: {
                enabled: true,
                textAnchor: 'start',
                style: {
                    colors: ['#fff'],
                    fontSize: '12px',
                    fontWeight: 600
                },
                formatter: function(val, opt) {
                    return val + " apos";
                },
                offsetX: window.innerWidth <= 768 ? 10 : 45
            },
            xaxis: {
                categories: gameLabels.slice().reverse(),
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
                xaxis: { lines: { show: true } },
                yaxis: { lines: { show: false } }
            },
            theme: chartTheme,
            tooltip: {
                theme: 'dark',
                y: {
                    formatter: function(val, opts) {
                        const index = gameData.length - 1 - opts.dataPointIndex;
                        const amount = gameAmounts[index] || 0;
                        return val + ' apostas | R$ ' + amount.toLocaleString('pt-BR');
                    }
                }
            },
            legend: {
                show: false
            }
        };
        
        window.Top5GamesCharts.barChart = new ApexCharts(barContainer, barOptions);
        window.Top5GamesCharts.barChart.render();
        console.log('✅ Bar Chart renderizado');
    }
    
    // 2. Radial Bar Chart
    const radialContainer = document.querySelector('#top5-games-radial-chart');
    if (radialContainer) {
        radialContainer.innerHTML = '';
        
        // Calculate percentages for radial chart
        const maxValue = Math.max(...gameData);
        const percentages = gameData.map(val => Math.round((val / maxValue) * 100));
        
        const radialOptions = {
            series: percentages.slice(0, 3), // Show only top 3 for better visualization
            chart: {
                type: 'radialBar',
                height: 280,
                background: 'transparent'
            },
            plotOptions: {
                radialBar: {
                    offsetY: 0,
                    startAngle: 0,
                    endAngle: 270,
                    hollow: {
                        margin: 5,
                        size: '30%',
                        background: 'transparent',
                        image: undefined
                    },
                    dataLabels: {
                        name: {
                            show: true,
                            fontSize: '14px',
                            color: '#94a3b8'
                        },
                        value: {
                            show: true,
                            fontSize: '20px',
                            color: '#fff',
                            formatter: function(val) {
                                return parseInt(val) + '%';
                            }
                        }
                    },
                    track: {
                        background: '#1e293b',
                        strokeWidth: '100%',
                        margin: 10
                    }
                }
            },
            colors: gameColors.slice(0, 3),
            labels: gameLabels.slice(0, 3),
            theme: chartTheme,
            legend: {
                show: true,
                floating: true,
                fontSize: '11px',
                position: 'bottom',
                offsetX: 0,
                offsetY: -10,
                labels: {
                    colors: '#94a3b8'
                },
                markers: {
                    size: 4
                },
                formatter: function(seriesName, opts) {
                    return seriesName.substring(0, 12) + ": " + gameData[opts.seriesIndex];
                },
                itemMargin: {
                    horizontal: 10,
                    vertical: 2
                }
            },
            responsive: [{
                breakpoint: 768,
                options: {
                    legend: {
                        show: true,
                        position: 'bottom',
                        offsetY: 0,
                        fontSize: '10px'
                    }
                }
            }, {
                breakpoint: 480,
                options: {
                    legend: {
                        show: false
                    }
                }
            }]
        };
        
        window.Top5GamesCharts.radialChart = new ApexCharts(radialContainer, radialOptions);
        window.Top5GamesCharts.radialChart.render();
        console.log('✅ Radial Chart renderizado');
    }
    
    // Mark as initialized
    window.Top5GamesCharts.initialized = true;
    console.log('✅ Todos os gráficos Top 5 Games renderizados com sucesso');
}

// Refresh function
window.refreshTop5Games = function() {
    console.log('🔄 Atualizando gráficos Top 5 Games...');
    window.Top5GamesCharts.initialized = false;
    
    // Add loading animation
    const containers = ['#top5-games-bar-chart', '#top5-games-radial-chart'];
    containers.forEach(selector => {
        const el = document.querySelector(selector);
        if (el) {
            el.innerHTML = '<div class="flex items-center justify-center h-full"><div class="text-green-400 animate-pulse">Carregando...</div></div>';
        }
    });
    
    // Reinitialize after a short delay
    setTimeout(initTop5GamesCharts, 300);
};

// Initialize on load
setTimeout(initTop5GamesCharts, 100);

// Reinitialize on Livewire navigation
document.addEventListener('livewire:navigated', function() {
    console.log('🔄 Livewire navigated - reinicializando Top 5 Games...');
    window.Top5GamesCharts.initialized = false;
    setTimeout(initTop5GamesCharts, 100);
});

// Reinitialize on component update
Livewire.hook('morph.updated', ({ el, component }) => {
    if (el.querySelector && (el.querySelector('#top5-games-bar-chart') || el.querySelector('#top5-games-radial-chart'))) {
        console.log('🔄 Componente Top 5 Games atualizado - reinicializando...');
        window.Top5GamesCharts.initialized = false;
        setTimeout(initTop5GamesCharts, 100);
    }
});
</script>
@endscript