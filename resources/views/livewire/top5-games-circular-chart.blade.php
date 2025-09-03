<div class="bg-gray-900 rounded-lg shadow-lg p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-bold text-white">🎮 TOP 5 JOGOS MAIS JOGADOS</h3>
        <button 
            class="text-green-400 hover:text-green-300 text-sm font-medium"
            onclick="window.showTop5Modal()"
        >
            Ver Detalhes
        </button>
    </div>
    
    @if(count($chartData['labels']) > 0)
        <div class="relative h-80 flex justify-center">
            <canvas id="top5GamesChart" width="300" height="300"></canvas>
        </div>
        
        <div class="mt-4 grid grid-cols-1 gap-2">
            @foreach($chartData['labels'] as $index => $game)
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $chartData['colors'][$index] ?? '#00ff7f' }}"></div>
                        <span class="text-white">{{ $game }}</span>
                    </div>
                    <div class="text-green-400 font-medium">
                        {{ $chartData['data'][$index] }} apostas
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-gray-400 mb-2">📊</div>
            <p class="text-gray-400">Nenhuma aposta registrada ainda</p>
        </div>
    @endif
</div>

<!-- Modal para detalhes -->
<div id="top5Modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-gray-800 rounded-lg max-w-4xl w-full p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-white">📊 TOP 5 CATEGORIAS COMPLETAS</h2>
                <button onclick="window.closeTop5Modal()" class="text-gray-400 hover:text-white">✕</button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Gráfico circular -->
                <div class="bg-gray-900 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-white mb-4">🎮 Jogos Mais Jogados</h3>
                    <canvas id="modalCircularChart" width="250" height="250"></canvas>
                </div>
                
                <!-- Tabela com valores reais -->
                <div class="bg-gray-900 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-white mb-4">💰 Valores Reais</h3>
                    <div class="space-y-3">
                        @if(count($chartData['labels']) > 0)
                            @foreach($chartData['labels'] as $index => $game)
                                <div class="flex justify-between items-center border-b border-gray-700 pb-2">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $chartData['colors'][$index] ?? '#00ff7f' }}"></div>
                                        <span class="text-white font-medium">{{ $game }}</span>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-green-400 font-bold">{{ $chartData['data'][$index] }} apostas</div>
                                        <div class="text-gray-400 text-sm">R$ {{ number_format($chartData['amounts'][$index] ?? 0, 2, ',', '.') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
let chartInstance = null;
let modalChartInstance = null;

function initTop5Charts() {
    @if(count($chartData['labels']) == 0)
        return;
    @endif
    
    const ctx = document.getElementById('top5GamesChart');
    if (ctx && !chartInstance) {
        chartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartData['labels']) !!},
                datasets: [{
                    data: {!! json_encode($chartData['data']) !!},
                    backgroundColor: {!! json_encode($chartData['colors']) !!},
                    borderColor: '#1f2937',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        titleColor: '#ffffff',
                        bodyColor: '#10b981',
                        borderColor: '#10b981',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} apostas (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '50%'
            }
        });
    }
}

window.showTop5Modal = function() {
    document.getElementById('top5Modal').classList.remove('hidden');
    
    setTimeout(() => {
        const modalCtx = document.getElementById('modalCircularChart');
        if (modalCtx && !modalChartInstance) {
            modalChartInstance = new Chart(modalCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($chartData['labels']) !!},
                    datasets: [{
                        data: {!! json_encode($chartData['data']) !!},
                        backgroundColor: {!! json_encode($chartData['colors']) !!},
                        borderColor: '#1f2937',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#ffffff',
                                padding: 20,
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            titleColor: '#ffffff',
                            bodyColor: '#10b981',
                            borderColor: '#10b981',
                            borderWidth: 1
                        }
                    },
                    cutout: '40%'
                }
            });
        }
    }, 100);
}

window.closeTop5Modal = function() {
    document.getElementById('top5Modal').classList.add('hidden');
    if (modalChartInstance) {
        modalChartInstance.destroy();
        modalChartInstance = null;
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', initTop5Charts);

// Initialize on Livewire navigation
document.addEventListener('livewire:navigated', initTop5Charts);
</script>
@endscript