<!-- INFOGRÁFICO PROFISSIONAL: TOP 5 JOGOS POPULARES -->
<div class="infographic-container bg-gradient-to-br from-slate-900 to-slate-800 rounded-2xl shadow-2xl border border-slate-700 overflow-hidden">
    @if(count($chartData['labels']) > 0)
        <!-- HEADER SECTION -->
        <div class="infographic-header bg-gradient-to-r from-green-600 to-green-500 p-6 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-green-600/20 to-transparent"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="bg-white/20 p-3 rounded-full">
                            <div class="text-2xl">🎮</div>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-white mb-1">TOP 5 JOGOS MAIS POPULARES</h2>
                            <p class="text-green-100 text-sm font-medium">Análise de Performance em Tempo Real</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="bg-white/20 rounded-xl p-3">
                            <div class="text-white text-2xl font-bold">{{ count($chartData['labels']) }}</div>
                            <div class="text-green-100 text-xs font-medium">JOGOS ATIVOS</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Decorative Elements -->
            <div class="absolute top-4 right-4 w-32 h-32 bg-white/5 rounded-full"></div>
            <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-white/5 rounded-full"></div>
        </div>

        <!-- MAIN INFOGRAPHIC CONTENT -->
        <div class="p-6 space-y-6">
            
            <!-- KPI METRICS ROW -->
            <div class="grid grid-cols-3 gap-4">
                <div class="metric-card bg-gradient-to-br from-blue-600/20 to-blue-500/10 border border-blue-500/30 rounded-xl p-4 text-center">
                    <div class="text-blue-400 text-2xl font-bold">{{ array_sum($chartData['data']) }}</div>
                    <div class="text-slate-300 text-xs font-medium mt-1">TOTAL APOSTAS</div>
                    <div class="w-full bg-slate-700 rounded-full h-1 mt-2">
                        <div class="bg-blue-400 h-1 rounded-full" style="width: 100%"></div>
                    </div>
                </div>
                
                <div class="metric-card bg-gradient-to-br from-green-600/20 to-green-500/10 border border-green-500/30 rounded-xl p-4 text-center">
                    <div class="text-green-400 text-2xl font-bold">R$ {{ number_format(array_sum($chartData['amounts']), 0, ',', '.') }}</div>
                    <div class="text-slate-300 text-xs font-medium mt-1">RECEITA TOTAL</div>
                    <div class="w-full bg-slate-700 rounded-full h-1 mt-2">
                        <div class="bg-green-400 h-1 rounded-full" style="width: 90%"></div>
                    </div>
                </div>
                
                <div class="metric-card bg-gradient-to-br from-purple-600/20 to-purple-500/10 border border-purple-500/30 rounded-xl p-4 text-center">
                    <div class="text-purple-400 text-2xl font-bold">{{ round(array_sum($chartData['amounts']) / array_sum($chartData['data']), 0) }}</div>
                    <div class="text-slate-300 text-xs font-medium mt-1">VALOR MÉDIO</div>
                    <div class="w-full bg-slate-700 rounded-full h-1 mt-2">
                        <div class="bg-purple-400 h-1 rounded-full" style="width: 75%"></div>
                    </div>
                </div>
            </div>

            <!-- MAIN ANALYSIS SECTION -->
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
                
                <!-- DONUT CHART SECTION -->
                <div class="lg:col-span-2">
                    <div class="bg-gradient-to-br from-slate-800 to-slate-700 rounded-xl p-6 border border-slate-600">
                        <div class="text-center mb-4">
                            <h3 class="text-white font-bold text-lg mb-2">Distribuição por Popularidade</h3>
                            <div class="text-slate-400 text-sm">Baseado no número de apostas</div>
                        </div>
                        
                        <div class="relative flex justify-center">
                            <canvas id="top5GamesChart" width="250" height="250"></canvas>
                            <!-- Center Stats -->
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <div class="text-center">
                                    <div class="text-green-400 text-2xl font-bold animate-pulse">
                                        {{ array_sum($chartData['data']) }}
                                    </div>
                                    <div class="text-slate-400 text-xs font-medium">TOTAL</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Legend -->
                        <div class="mt-4 grid grid-cols-1 gap-2">
                            @foreach($chartData['labels'] as $index => $game)
                                @if($index < 3)
                                <div class="flex items-center justify-between text-sm">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-3 h-3 rounded-full" style="background-color: {{ $chartData['colors'][$index] }}"></div>
                                        <span class="text-white text-xs">{{ $game }}</span>
                                    </div>
                                    <span class="text-slate-300 font-medium">{{ number_format((($chartData['data'][$index] / array_sum($chartData['data'])) * 100), 1) }}%</span>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- RANKING SECTION -->
                <div class="lg:col-span-3">
                    <div class="bg-gradient-to-br from-slate-800 to-slate-700 rounded-xl p-6 border border-slate-600">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-white font-bold text-lg">🏆 Ranking Detalhado</h3>
                            <div class="bg-green-500/20 text-green-400 px-3 py-1 rounded-full text-xs font-medium">
                                TEMPO REAL
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            @foreach($chartData['labels'] as $index => $game)
                                <div class="ranking-item bg-gradient-to-r from-slate-700/50 to-transparent rounded-xl p-4 border border-slate-600/50 hover:border-green-500/50 transition-all duration-300 relative overflow-hidden">
                                    
                                    <!-- Position Badge -->
                                    <div class="absolute top-3 left-3">
                                        <div class="ranking-badge w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold text-white" 
                                             style="background: linear-gradient(135deg, {{ $chartData['colors'][$index] }}, {{ $chartData['colors'][$index] }}80);">
                                            {{ $index + 1 }}
                                        </div>
                                    </div>
                                    
                                    <!-- Game Info -->
                                    <div class="ml-12">
                                        <div class="flex items-center justify-between mb-2">
                                            <div>
                                                <div class="text-white font-semibold text-base">{{ $game }}</div>
                                                <div class="text-slate-400 text-xs">
                                                    @if($index == 0) 👑 LÍDER ABSOLUTO
                                                    @elseif($index == 1) 🥈 VICE-LÍDER  
                                                    @elseif($index == 2) 🥉 TOP 3
                                                    @else ⭐ TOP {{ $index + 1 }} @endif
                                                </div>
                                            </div>
                                            
                                            <div class="text-right">
                                                <div class="text-green-400 font-bold text-lg">{{ $chartData['data'][$index] }}</div>
                                                <div class="text-slate-400 text-xs">apostas</div>
                                            </div>
                                        </div>
                                        
                                        <!-- Performance Bar -->
                                        <div class="w-full bg-slate-600 rounded-full h-2 overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-1000 ease-out"
                                                 style="width: {{ ($chartData['data'][$index] / max($chartData['data'])) * 100 }}%; 
                                                        background: linear-gradient(90deg, {{ $chartData['colors'][$index] }}, {{ $chartData['colors'][$index] }}80);">
                                            </div>
                                        </div>
                                        
                                        <!-- Revenue Info -->
                                        @if(isset($chartData['amounts'][$index]))
                                        <div class="flex justify-between items-center mt-2 text-xs">
                                            <span class="text-slate-400">Receita:</span>
                                            <span class="text-green-300 font-medium">R$ {{ number_format($chartData['amounts'][$index], 2, ',', '.') }}</span>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Hover Effect -->
                                    <div class="absolute inset-0 bg-gradient-to-r from-green-500/5 to-transparent opacity-0 hover:opacity-100 transition-opacity duration-300 rounded-xl"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- SUMMARY FOOTER -->
            <div class="bg-gradient-to-r from-green-600/10 to-blue-600/10 rounded-xl p-4 border border-green-500/20">
                <div class="text-center">
                    <div class="text-white font-medium text-sm mb-2">
                        💡 <strong>Insights:</strong> 
                        <span class="text-slate-300">
                            O jogo líder representa {{ number_format((max($chartData['data']) / array_sum($chartData['data'])) * 100, 1) }}% 
                            do total de apostas da plataforma
                        </span>
                    </div>
                    <div class="flex justify-center space-x-6 text-xs text-slate-400">
                        <span>📊 Atualização: Tempo Real</span>
                        <span>🎯 Precisão: 99.9%</span>
                        <span>🔄 Cache: 15min</span>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- EMPTY STATE -->
        <div class="infographic-empty text-center py-20 px-6">
            <div class="max-w-md mx-auto">
                <div class="bg-gradient-to-br from-slate-700 to-slate-600 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                    <div class="text-4xl opacity-50">🎮</div>
                </div>
                <h3 class="text-white font-bold text-xl mb-2">Dashboard Aguardando Dados</h3>
                <p class="text-slate-400 text-sm mb-6">O sistema está preparado para exibir análises assim que os primeiros jogos receberem apostas.</p>
                
                <div class="bg-slate-800 rounded-xl p-4 border border-slate-600">
                    <div class="flex items-center justify-center space-x-2 text-green-400 text-sm">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="font-medium">Sistema de Monitoramento Ativo</span>
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.infographic-container {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.metric-card {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.metric-card::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.03), transparent);
    transform: rotate(45deg);
    transition: all 0.6s;
}

.metric-card:hover::before {
    animation: shimmer 1.5s infinite;
}

.metric-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

@keyframes shimmer {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}

.ranking-item {
    position: relative;
    backdrop-filter: blur(10px);
}

.ranking-badge {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

.infographic-header {
    position: relative;
    background-attachment: fixed;
}
</style>

@script
<script>
let top5InfographicChartInstance = null;

function initTop5InfographicChart() {
    @if(count($chartData['labels']) == 0)
        return;
    @endif
    
    if (typeof Chart === 'undefined') {
        console.log('Chart.js carregando...');
        setTimeout(initTop5InfographicChart, 100);
        return;
    }
    
    const ctx = document.getElementById('top5GamesChart');
    if (ctx && !top5InfographicChartInstance) {
        top5InfographicChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartData['labels']) !!},
                datasets: [{
                    data: {!! json_encode($chartData['data']) !!},
                    backgroundColor: {!! json_encode($chartData['colors']) !!},
                    borderColor: '#1e293b',
                    borderWidth: 4,
                    hoverBorderWidth: 6,
                    hoverBorderColor: '#22c55e'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        titleColor: '#ffffff',
                        bodyColor: '#22c55e',
                        borderColor: '#22c55e',
                        borderWidth: 2,
                        cornerRadius: 12,
                        padding: 16,
                        displayColors: true,
                        callbacks: {
                            title: function(context) {
                                return context[0].label;
                            },
                            label: function(context) {
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return [
                                    `🎯 Apostas: ${value}`,
                                    `📊 Participação: ${percentage}%`,
                                    @if(isset($chartData['amounts']))
                                        `💰 Receita: R$ {{ number_format($chartData['amounts'][0] ?? 0, 2, ',', '.') }}`
                                    @endif
                                ];
                            }
                        }
                    }
                },
                cutout: '70%',
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 2000,
                    easing: 'easeInOutQuart'
                },
                elements: {
                    arc: {
                        borderRadius: 8
                    }
                }
            }
        });
    }
}

// Cleanup and initialization
document.addEventListener('DOMContentLoaded', function() {
    initTop5InfographicChart();
});

document.addEventListener('livewire:navigated', function() {
    if (top5InfographicChartInstance) {
        top5InfographicChartInstance.destroy();
        top5InfographicChartInstance = null;
    }
    setTimeout(initTop5InfographicChart, 100);
});
</script>
@endscript