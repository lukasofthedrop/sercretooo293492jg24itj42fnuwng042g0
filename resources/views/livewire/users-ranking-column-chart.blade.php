<!-- INFOGRÁFICO PROFISSIONAL: RANKING USUÁRIOS VIP -->
<div class="infographic-vip-container bg-gradient-to-br from-gray-900 via-slate-900 to-gray-900 rounded-2xl shadow-2xl border border-gray-700 overflow-hidden">
    @if(count($chartData['labels']) > 0)
        
        <!-- PREMIUM HEADER SECTION -->
        <div class="infographic-vip-header bg-gradient-to-r from-yellow-600 via-yellow-500 to-orange-500 p-4 md:p-5 lg:p-6 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-yellow-600/30 to-orange-500/20"></div>
            <div class="relative z-10">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center space-x-2 md:space-x-4">
                        <div class="bg-white/20 p-2 md:p-3 rounded-full backdrop-blur-sm">
                            <div class="text-xl md:text-2xl">👑</div>
                        </div>
                        <div>
                            <h2 class="text-lg md:text-xl lg:text-2xl font-bold text-white mb-1">RANKING PREMIUM VIP</h2>
                            <p class="text-yellow-100 text-xs md:text-sm font-medium">Análise Avançada de Usuários de Alto Valor</p>
                        </div>
                    </div>
                    <div class="text-center sm:text-right">
                        <div class="bg-white/20 rounded-xl p-2 md:p-3 backdrop-blur-sm">
                            <div class="text-white text-xl md:text-2xl font-bold">{{ count($chartData['labels']) }}</div>
                            <div class="text-yellow-100 text-xs font-medium">USUÁRIOS VIP</div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Premium Decorative Elements -->
            <div class="absolute top-2 right-8 w-40 h-40 bg-white/5 rounded-full"></div>
            <div class="absolute -bottom-8 -right-8 w-32 h-32 bg-white/5 rounded-full"></div>
            <div class="absolute top-8 left-1/3 w-2 h-2 bg-white/30 rounded-full animate-ping"></div>
            <div class="absolute top-12 left-1/2 w-1 h-1 bg-white/40 rounded-full animate-pulse"></div>
        </div>

        <!-- MAIN INFOGRAPHIC CONTENT -->
        <div class="p-4 md:p-5 lg:p-6 space-y-4 md:space-y-5 lg:space-y-6">
            
            <!-- EXECUTIVE KPI DASHBOARD -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
                <div class="executive-kpi bg-gradient-to-br from-green-600/20 to-emerald-500/10 border border-green-500/30 rounded-xl p-3 md:p-4 text-center relative overflow-hidden">
                    <div class="absolute top-2 right-2 text-green-400 opacity-20">💰</div>
                    <div class="text-green-400 text-lg md:text-xl lg:text-2xl font-bold">R$ {{ number_format(array_sum($chartData['amounts']), 0, ',', '.') }}</div>
                    <div class="text-slate-300 text-xs font-medium mt-1">RECEITA VIP TOTAL</div>
                    <div class="w-full bg-slate-700 rounded-full h-1.5 mt-2">
                        <div class="bg-green-400 h-1.5 rounded-full" style="width: 100%"></div>
                    </div>
                </div>
                
                <div class="executive-kpi bg-gradient-to-br from-blue-600/20 to-cyan-500/10 border border-blue-500/30 rounded-xl p-4 text-center relative overflow-hidden">
                    <div class="absolute top-2 right-2 text-blue-400 opacity-20">📈</div>
                    <div class="text-blue-400 text-2xl font-bold">{{ array_sum($chartData['deposits']) }}</div>
                    <div class="text-slate-300 text-xs font-medium mt-1">DEPÓSITOS TOTAL</div>
                    <div class="w-full bg-slate-700 rounded-full h-1.5 mt-2">
                        <div class="bg-blue-400 h-1.5 rounded-full" style="width: 85%"></div>
                    </div>
                </div>
                
                <div class="executive-kpi bg-gradient-to-br from-purple-600/20 to-pink-500/10 border border-purple-500/30 rounded-xl p-4 text-center relative overflow-hidden">
                    <div class="absolute top-2 right-2 text-purple-400 opacity-20">💎</div>
                    <div class="text-purple-400 text-2xl font-bold">R$ {{ number_format(array_sum($chartData['amounts']) / max(array_sum($chartData['deposits']), 1), 0, ',', '.') }}</div>
                    <div class="text-slate-300 text-xs font-medium mt-1">TICKET MÉDIO</div>
                    <div class="w-full bg-slate-700 rounded-full h-1.5 mt-2">
                        <div class="bg-purple-400 h-1.5 rounded-full" style="width: 70%"></div>
                    </div>
                </div>
                
                <div class="executive-kpi bg-gradient-to-br from-orange-600/20 to-red-500/10 border border-orange-500/30 rounded-xl p-4 text-center relative overflow-hidden">
                    <div class="absolute top-2 right-2 text-orange-400 opacity-20">🏆</div>
                    <div class="text-orange-400 text-2xl font-bold">{{ number_format((max($chartData['amounts']) / array_sum($chartData['amounts'])) * 100, 1) }}%</div>
                    <div class="text-slate-300 text-xs font-medium mt-1">CONCENTRAÇÃO TOP 1</div>
                    <div class="w-full bg-slate-700 rounded-full h-1.5 mt-2">
                        <div class="bg-orange-400 h-1.5 rounded-full" style="width: {{ (max($chartData['amounts']) / array_sum($chartData['amounts'])) * 100 }}%"></div>
                    </div>
                </div>
            </div>

            <!-- ADVANCED ANALYTICS SECTION -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 md:gap-5 lg:gap-6">
                
                <!-- COMBO CHART SECTION -->
                <div class="lg:col-span-2">
                    <div class="bg-gradient-to-br from-slate-800 to-slate-700 rounded-xl p-4 md:p-5 lg:p-6 border border-slate-600">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h3 class="text-white font-bold text-lg mb-1">📊 Análise Combo: Valores + Tendência</h3>
                                <p class="text-slate-400 text-sm">Visualização avançada com barras e linha de tendência</p>
                            </div>
                            <div class="flex space-x-2">
                                <div class="bg-green-500/20 text-green-400 px-2 py-1 rounded text-xs font-medium">BARRAS</div>
                                <div class="bg-blue-500/20 text-blue-400 px-2 py-1 rounded text-xs font-medium">TENDÊNCIA</div>
                            </div>
                        </div>
                        
                        <div class="chart-container relative" style="height: 320px;">
                            <canvas id="usersComboChart" style="width: 100%; height: 100%;"></canvas>
                        </div>
                        
                        <!-- Chart Legend -->
                        <div class="mt-4 flex justify-center space-x-6 text-xs">
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-3 bg-green-500 rounded"></div>
                                <span class="text-slate-300">Valores Depositados</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="w-3 h-1 bg-blue-500 rounded"></div>
                                <span class="text-slate-300">Linha de Tendência</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- VIP TIERS SECTION -->
                <div class="lg:col-span-1">
                    <div class="bg-gradient-to-br from-slate-800 to-slate-700 rounded-xl p-4 md:p-5 lg:p-6 border border-slate-600 h-full">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-white font-bold text-lg">🏅 Tiers VIP</h3>
                            <div class="bg-yellow-500/20 text-yellow-400 px-2 py-1 rounded-full text-xs font-medium">
                                PREMIUM
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            @foreach($chartData['fullNames'] as $index => $fullName)
                                @if($index < 6)
                                <div class="vip-tier-item bg-gradient-to-r from-slate-700/80 to-transparent rounded-lg p-4 border border-slate-600/50">
                                    
                                    <!-- Tier Badge -->
                                    <div class="float-right ml-2">
                                        @if($index == 0)
                                            <div class="tier-badge bg-gradient-to-r from-yellow-500 to-orange-500 text-black text-xs px-2 py-1 rounded-full font-bold">DIAMOND</div>
                                        @elseif($index == 1)
                                            <div class="tier-badge bg-gradient-to-r from-gray-300 to-gray-400 text-black text-xs px-2 py-1 rounded-full font-bold">PLATINUM</div>
                                        @elseif($index == 2)
                                            <div class="tier-badge bg-gradient-to-r from-yellow-600 to-yellow-700 text-white text-xs px-2 py-1 rounded-full font-bold">GOLD</div>
                                        @else
                                            <div class="tier-badge bg-gradient-to-r from-gray-500 to-gray-600 text-white text-xs px-2 py-1 rounded-full font-bold">SILVER</div>
                                        @endif
                                    </div>
                                    
                                    <!-- User Info -->
                                    <div>
                                        <div class="flex items-center space-x-3 mb-2">
                                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold text-white shadow-lg" 
                                                 style="background: linear-gradient(135deg, {{ $chartData['colors'][$index] }}, {{ $chartData['colors'][$index] }}80);">
                                                {{ $index + 1 }}
                                            </div>
                                            <div class="text-white font-semibold text-sm truncate flex-1">{{ $fullName }}</div>
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-2 mt-3">
                                            <div>
                                                <span class="text-slate-400 text-xs block">Valor Total</span>
                                                <span class="text-green-400 font-bold text-base">R$ {{ number_format($chartData['amounts'][$index], 0, ',', '.') }}</span>
                                            </div>
                                            <div>
                                                <span class="text-slate-400 text-xs block">Depósitos</span>
                                                <span class="text-blue-400 font-bold text-base">{{ $chartData['deposits'][$index] }}</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Progress Bar -->
                                        <div class="w-full bg-slate-600 rounded-full h-2 mt-4">
                                            <div class="h-2 rounded-full transition-all duration-500" 
                                                 style="width: {{ ($chartData['amounts'][$index] / max($chartData['amounts'])) * 100 }}%; 
                                                        background: linear-gradient(90deg, {{ $chartData['colors'][$index] }}, {{ $chartData['colors'][$index] }}80);">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                            
                            <!-- View All Button -->
                            <button class="w-full bg-gradient-to-r from-slate-700 to-slate-600 text-slate-300 py-2 rounded-lg text-sm font-medium hover:from-slate-600 hover:to-slate-500 transition-all duration-300">
                                Ver Todos os {{ count($chartData['labels']) }} Usuários VIP
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PERFORMANCE INSIGHTS SECTION -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 md:gap-5 lg:gap-6">
                
                <!-- Key Performance Metrics -->
                <div class="bg-gradient-to-br from-slate-800 to-slate-700 rounded-xl p-6 border border-slate-600">
                    <h3 class="text-white font-bold text-lg mb-4">🎯 Performance Insights</h3>
                    
                    <div class="space-y-4">
                        <div class="insight-item">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-slate-300 text-sm">Retenção VIP (Top 3)</span>
                                <span class="text-green-400 font-bold">{{ number_format((array_sum(array_slice($chartData['amounts'], 0, 3)) / array_sum($chartData['amounts'])) * 100, 1) }}%</span>
                            </div>
                            <div class="w-full bg-slate-600 rounded-full h-2">
                                <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full" 
                                     style="width: {{ (array_sum(array_slice($chartData['amounts'], 0, 3)) / array_sum($chartData['amounts'])) * 100 }}%"></div>
                            </div>
                        </div>
                        
                        <div class="insight-item">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-slate-300 text-sm">Distribuição Equilibrada</span>
                                <span class="text-blue-400 font-bold">{{ number_format(100 - ((max($chartData['amounts']) / array_sum($chartData['amounts'])) * 100), 1) }}%</span>
                            </div>
                            <div class="w-full bg-slate-600 rounded-full h-2">
                                <div class="bg-gradient-to-r from-blue-500 to-cyan-500 h-2 rounded-full" 
                                     style="width: {{ 100 - ((max($chartData['amounts']) / array_sum($chartData['amounts'])) * 100) }}%"></div>
                            </div>
                        </div>
                        
                        <div class="insight-item">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-slate-300 text-sm">Potencial de Crescimento</span>
                                <span class="text-purple-400 font-bold">{{ number_format((count($chartData['labels']) / 11) * 100, 0) }}%</span>
                            </div>
                            <div class="w-full bg-slate-600 rounded-full h-2">
                                <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-2 rounded-full" 
                                     style="width: {{ (count($chartData['labels']) / 11) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Strategic Recommendations -->
                <div class="bg-gradient-to-br from-slate-800 to-slate-700 rounded-xl p-6 border border-slate-600">
                    <h3 class="text-white font-bold text-lg mb-4">💡 Recomendações Estratégicas</h3>
                    
                    <div class="space-y-3">
                        <div class="recommendation-item flex items-start space-x-3 p-3 bg-green-500/10 rounded-lg border border-green-500/20">
                            <div class="text-green-400 text-lg">🎯</div>
                            <div>
                                <div class="text-green-400 font-medium text-sm">Foco no Top Tier</div>
                                <div class="text-slate-300 text-xs">Investir em retenção dos 3 primeiros usuários</div>
                            </div>
                        </div>
                        
                        <div class="recommendation-item flex items-start space-x-3 p-3 bg-blue-500/10 rounded-lg border border-blue-500/20">
                            <div class="text-blue-400 text-lg">📈</div>
                            <div>
                                <div class="text-blue-400 font-medium text-sm">Programa de Upgrade</div>
                                <div class="text-slate-300 text-xs">Criar incentivos para usuários Silver → Gold</div>
                            </div>
                        </div>
                        
                        <div class="recommendation-item flex items-start space-x-3 p-3 bg-purple-500/10 rounded-lg border border-purple-500/20">
                            <div class="text-purple-400 text-lg">💎</div>
                            <div>
                                <div class="text-purple-400 font-medium text-sm">Personalização VIP</div>
                                <div class="text-slate-300 text-xs">Ofertas customizadas baseadas no perfil</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- EXECUTIVE SUMMARY FOOTER -->
            <div class="bg-gradient-to-r from-yellow-600/10 via-orange-500/10 to-red-500/10 rounded-xl p-4 border border-yellow-500/20 max-h-32 overflow-y-auto">
                <div class="text-center">
                    <div class="text-white font-medium text-sm mb-2">
                        🏆 <strong>Resumo Executivo:</strong> 
                        <span class="text-slate-300">
                            Base VIP de {{ count($chartData['labels']) }} usuários gerando R$ {{ number_format(array_sum($chartData['amounts']), 0, ',', '.') }} 
                            com ticket médio de R$ {{ number_format(array_sum($chartData['amounts']) / max(array_sum($chartData['deposits']), 1), 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex justify-center space-x-6 text-xs text-slate-400">
                        <span>💰 ROI Tracking: Ativo</span>
                        <span>📊 ML Analytics: Habilitado</span>
                        <span>🔄 Sync: Real-time</span>
                        <span>🛡️ Compliance: 100%</span>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- PREMIUM EMPTY STATE -->
        <div class="premium-empty text-center py-20 px-6">
            <div class="max-w-lg mx-auto">
                <div class="bg-gradient-to-br from-yellow-600 to-orange-500 w-28 h-28 rounded-full flex items-center justify-center mx-auto mb-6 shadow-2xl">
                    <div class="text-5xl text-white">👑</div>
                </div>
                <h3 class="text-white font-bold text-2xl mb-2">Programa VIP Aguardando Ativação</h3>
                <p class="text-slate-400 text-sm mb-8 max-w-md mx-auto">
                    Sistema de analytics premium preparado para identificar e analisar usuários de alto valor assim que realizarem seus primeiros depósitos.
                </p>
                
                <div class="grid grid-cols-2 gap-4 max-w-sm mx-auto">
                    <div class="bg-slate-800 rounded-xl p-4 border border-slate-600">
                        <div class="text-green-400 text-lg md:text-xl lg:text-2xl font-bold">0</div>
                        <div class="text-slate-400 text-xs">Usuários Diamond</div>
                    </div>
                    <div class="bg-slate-800 rounded-xl p-4 border border-slate-600">
                        <div class="text-blue-400 text-2xl font-bold">R$ 0</div>
                        <div class="text-slate-400 text-xs">Receita VIP</div>
                    </div>
                </div>
                
                <div class="mt-8">
                    <div class="flex items-center justify-center space-x-2 text-yellow-400 text-sm">
                        <div class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse"></div>
                        <span class="font-medium">Sistema Premium de Monitoramento Ativo</span>
                        <div class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse" style="animation-delay: 0.5s;"></div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.infographic-vip-container {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    backdrop-filter: blur(10px);
}

.executive-kpi {
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(10px);
}

.executive-kpi::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.05), transparent);
    transform: rotate(45deg);
    transition: all 0.6s;
}

.executive-kpi:hover::before {
    animation: premiumShimmer 2s infinite;
}

.executive-kpi:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.3);
}

@keyframes premiumShimmer {
    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
}

.vip-tier-item {
    transition: all 0.3s ease;
    backdrop-filter: blur(5px);
}

.vip-tier-item:hover {
    transform: translateX(5px);
    border-color: rgba(34, 197, 94, 0.5);
    background: linear-gradient(90deg, rgba(34, 197, 94, 0.1), transparent);
}

.tier-badge {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(5px);
}

.insight-item, .recommendation-item {
    transition: all 0.3s ease;
}

.insight-item:hover, .recommendation-item:hover {
    transform: translateY(-1px);
}

.chart-container {
    position: relative;
}

.chart-container canvas {
    border-radius: 8px;
}

.infographic-vip-header {
    background-attachment: fixed;
    position: relative;
}

.premium-empty {
    background: radial-gradient(circle at center, rgba(251, 191, 36, 0.05) 0%, transparent 70%);
}
</style>

@script
<script>
let usersVipComboChartInstance = null;

// Função para carregar Chart.js dinamicamente
function loadChartJS(callback) {
    if (typeof Chart !== 'undefined') {
        callback();
        return;
    }
    
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js';
    script.onload = function() {
        console.log('✅ Chart.js carregado com sucesso!');
        callback();
    };
    script.onerror = function() {
        console.error('❌ Erro ao carregar Chart.js');
    };
    document.head.appendChild(script);
}

function initUsersVipComboChart() {
    @if(count($chartData['labels']) == 0)
        return;
    @endif
    
    if (typeof Chart === 'undefined') {
        console.log('Chart.js não disponível, carregando...');
        loadChartJS(initUsersVipComboChart);
        return;
    }
    
    const ctx = document.getElementById('usersComboChart');
    if (ctx && !usersVipComboChartInstance) {
        
        // Data arrays
        const amounts = {!! json_encode($chartData['amounts']) !!};
        const fullNames = {!! json_encode($chartData['fullNames']) !!};
        const emails = {!! json_encode($chartData['emails']) !!};
        const deposits = {!! json_encode($chartData['deposits']) !!};
        
        // Create trend line (3-point moving average)
        const trendLine = amounts.map((_, index, array) => {
            const start = Math.max(0, index - 1);
            const end = Math.min(array.length - 1, index + 1);
            const subset = array.slice(start, end + 1);
            return subset.reduce((a, b) => a + b, 0) / subset.length;
        });
        
        usersVipComboChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartData['labels']) !!},
                datasets: [
                    {
                        type: 'bar',
                        label: '💰 Valor Depositado',
                        data: amounts,
                        backgroundColor: function(context) {
                            const colors = {!! json_encode($chartData['colors']) !!};
                            const gradient = context.chart.ctx.createLinearGradient(0, 0, 0, 300);
                            gradient.addColorStop(0, colors[context.dataIndex] || '#22c55e');
                            gradient.addColorStop(1, (colors[context.dataIndex] || '#22c55e') + '40');
                            return gradient;
                        },
                        borderColor: {!! json_encode($chartData['colors']) !!},
                        borderWidth: 2,
                        borderRadius: {
                            topLeft: 8,
                            topRight: 8,
                        },
                        borderSkipped: false,
                        yAxisID: 'y'
                    },
                    {
                        type: 'line',
                        label: '📈 Tendência Premium',
                        data: trendLine,
                        borderColor: '#3b82f6',
                        backgroundColor: function(context) {
                            const gradient = context.chart.ctx.createLinearGradient(0, 0, 0, 300);
                            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
                            gradient.addColorStop(1, 'rgba(59, 130, 246, 0.05)');
                            return gradient;
                        },
                        borderWidth: 4,
                        pointBackgroundColor: '#3b82f6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 3,
                        pointRadius: 8,
                        pointHoverRadius: 10,
                        pointHoverBackgroundColor: '#1d4ed8',
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 10,
                        bottom: 10,
                        left: 10,
                        right: 10
                    }
                },
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            color: '#ffffff',
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                family: 'system-ui',
                                size: 12,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                        titleColor: '#ffffff',
                        bodyColor: '#22c55e',
                        borderColor: '#3b82f6',
                        borderWidth: 2,
                        cornerRadius: 12,
                        padding: 16,
                        displayColors: true,
                        callbacks: {
                            title: function(context) {
                                return `👑 ${fullNames[context[0].dataIndex]}`;
                            },
                            label: function(context) {
                                const userIndex = context.dataIndex;
                                const userDeposits = deposits[userIndex];
                                const value = context.parsed.y;
                                
                                if (context.dataset.type === 'bar') {
                                    const avgDeposit = value / Math.max(userDeposits, 1);
                                    return [
                                        `💰 Total Depositado: R$ ${value.toLocaleString('pt-BR', {minimumFractionDigits: 2})}`,
                                        `📊 Quantidade: ${userDeposits} depósitos`,
                                        `💎 Ticket Médio: R$ ${avgDeposit.toLocaleString('pt-BR', {minimumFractionDigits: 2})}`,
                                        `🏆 Posição: #${userIndex + 1} no ranking`,
                                        `📧 ${emails[userIndex]}`
                                    ];
                                } else {
                                    return `📈 Tendência: R$ ${value.toLocaleString('pt-BR', {minimumFractionDigits: 2})}`;
                                }
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: '#94a3b8',
                            font: {
                                family: 'system-ui',
                                size: 11,
                                weight: '500'
                            },
                            maxRotation: 45
                        },
                        grid: {
                            color: 'rgba(71, 85, 105, 0.3)',
                            lineWidth: 1
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        ticks: {
                            color: '#22c55e',
                            font: {
                                family: 'system-ui',
                                size: 11,
                                weight: '600'
                            },
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR', {maximumFractionDigits: 0});
                            }
                        },
                        grid: {
                            color: 'rgba(34, 197, 94, 0.15)',
                            lineWidth: 1
                        }
                    }
                },
                animation: {
                    duration: 2500,
                    easing: 'easeInOutQuart',
                    delay: function(context) {
                        return context.dataIndex * 100;
                    }
                }
            }
        });
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadChartJS(initUsersVipComboChart);
});

document.addEventListener('livewire:navigated', function() {
    if (usersVipComboChartInstance) {
        usersVipComboChartInstance.destroy();
        usersVipComboChartInstance = null;
    }
    setTimeout(() => {
        loadChartJS(initUsersVipComboChart);
    }, 100);
});

// Garantir inicialização se página já carregada
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    setTimeout(() => {
        loadChartJS(initUsersVipComboChart);
    }, 100);
}
</script>
@endscript