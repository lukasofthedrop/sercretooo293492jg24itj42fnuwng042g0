<div class="bg-black rounded-lg shadow-lg p-6 border border-gray-800">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-bold text-white">👑 RANKING PROFISSIONAL DE USUÁRIOS</h3>
        <button 
            class="text-green-400 hover:text-green-300 text-sm font-medium transition-colors"
            onclick="window.showUsersRankingModal()"
        >
            Ver Detalhes Completos
        </button>
    </div>
    
    @if(count($chartData['labels']) > 0)
        <div class="relative h-80 mb-4">
            <canvas id="usersRankingChart"></canvas>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($chartData['labels'] as $index => $user)
                @if($index < 6)
                    <div class="flex items-center justify-between bg-gray-800 rounded-lg p-3">
                        <div class="flex items-center">
                            <div class="text-2xl mr-2">
                                @if($index == 0) 👑
                                @elseif($index == 1) 🥇
                                @elseif($index == 2) 🥈
                                @elseif($index == 3) 🥉
                                @else ⭐
                                @endif
                            </div>
                            <div>
                                <div class="text-white font-medium text-sm">{{ $chartData['fullNames'][$index] ?? $user }}</div>
                                <div class="text-gray-400 text-xs">{{ $chartData['deposits'][$index] }} depósitos</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-sm" style="color: {{ $chartData['colors'][$index] ?? '#00ff41' }}">R$ {{ number_format($chartData['amounts'][$index] ?? 0, 2, ',', '.') }}</div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <div class="text-center py-12">
            <div class="text-gray-400 mb-2 text-4xl">👑</div>
            <p class="text-gray-400">Nenhum depósito registrado ainda</p>
        </div>
    @endif
</div>

<!-- Modal para ranking completo -->
<div id="usersRankingModal" class="fixed inset-0 bg-black bg-opacity-80 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-black border border-gray-700 rounded-lg max-w-6xl w-full p-6 max-h-90vh overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-white">👑 RANKING COMPLETO DE USUÁRIOS</h2>
                <button onclick="window.closeUsersRankingModal()" class="text-green-400 hover:text-green-300 text-2xl transition-colors">✕</button>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Gráfico de colunas -->
                <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
                    <h3 class="text-lg font-semibold text-green-400 mb-4">📊 Gráfico de Colunas</h3>
                    <div class="h-80">
                        <canvas id="modalColumnChart"></canvas>
                    </div>
                </div>
                
                <!-- Tabela completa -->
                <div class="bg-gray-900 rounded-lg p-4 border border-gray-700">
                    <h3 class="text-lg font-semibold text-white mb-4">💰 Ranking Detalhado</h3>
                    <div class="space-y-2 max-h-80 overflow-y-auto">
                        @if(count($chartData['labels']) > 0)
                            @foreach($chartData['labels'] as $index => $user)
                                <div class="flex items-center justify-between border-b border-gray-700 pb-2 mb-2">
                                    <div class="flex items-center space-x-3">
                                        <div class="text-xl">
                                            @if($index == 0) 👑
                                            @elseif($index == 1) 🥇
                                            @elseif($index == 2) 🥈
                                            @elseif($index == 3) 🥉
                                            @else 
                                                <span class="bg-gray-700 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">{{ $index + 1 }}</span>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-white font-medium">{{ $chartData['fullNames'][$index] ?? $user }}</div>
                                            <div class="text-gray-400 text-xs">{{ $chartData['emails'][$index] ?? '' }}</div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-bold" style="color: {{ $chartData['colors'][$index] ?? '#00ff41' }}">R$ {{ number_format($chartData['amounts'][$index] ?? 0, 2, ',', '.') }}</div>
                                        <div class="text-gray-400 text-xs">{{ $chartData['deposits'][$index] }} depósitos</div>
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
let usersChartInstance = null;
let modalUsersChartInstance = null;

function initUsersChart() {
    @if(count($chartData['labels']) == 0)
        return;
    @endif
    
    const ctx = document.getElementById('usersRankingChart');
    if (ctx && !usersChartInstance) {
        usersChartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartData['labels']) !!},
                datasets: [{
                    label: 'Total Depositado (R$)',
                    data: {!! json_encode($chartData['amounts']) !!},
                    backgroundColor: {!! json_encode(array_slice($chartData['colors'], 0, count($chartData['labels']))) !!},
                    borderColor: '#000000',
                    borderWidth: 2,
                    borderRadius: 4
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
                        backgroundColor: '#000000',
                        titleColor: '#ffffff',
                        bodyColor: '#00ff41',
                        borderColor: '#00ff41',
                        borderWidth: 2,
                        callbacks: {
                            title: function(context) {
                                const index = context[0].dataIndex;
                                return {!! json_encode($chartData['fullNames']) !!}[index] || context[0].label;
                            },
                            label: function(context) {
                                const index = context.dataIndex;
                                const deposits = {!! json_encode($chartData['deposits']) !!}[index];
                                const value = context.parsed.y;
                                return [
                                    `Total: R$ ${value.toLocaleString('pt-BR', {minimumFractionDigits: 2})}`,
                                    `Depósitos: ${deposits}`
                                ];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#9ca3af',
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        },
                        grid: {
                            color: '#374151'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#9ca3af',
                            maxRotation: 45
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
}

window.showUsersRankingModal = function() {
    document.getElementById('usersRankingModal').classList.remove('hidden');
    
    setTimeout(() => {
        const modalCtx = document.getElementById('modalColumnChart');
        if (modalCtx && !modalUsersChartInstance) {
            modalUsersChartInstance = new Chart(modalCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($chartData['fullNames']) !!},
                    datasets: [{
                        label: 'Total Depositado (R$)',
                        data: {!! json_encode($chartData['amounts']) !!},
                        backgroundColor: {!! json_encode($chartData['colors']) !!},
                        borderColor: '#000000',
                        borderWidth: 2,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                color: '#ffffff',
                                padding: 20
                            }
                        },
                        tooltip: {
                            backgroundColor: '#000000',
                            titleColor: '#ffffff',
                            bodyColor: '#00ff41',
                            borderColor: '#00ff41',
                            borderWidth: 2
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: '#9ca3af',
                                callback: function(value) {
                                    return 'R$ ' + value.toLocaleString('pt-BR');
                                }
                            },
                            grid: {
                                color: '#374151'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#9ca3af',
                                maxRotation: 45
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    }, 100);
}

window.closeUsersRankingModal = function() {
    document.getElementById('usersRankingModal').classList.add('hidden');
    if (modalUsersChartInstance) {
        modalUsersChartInstance.destroy();
        modalUsersChartInstance = null;
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', initUsersChart);

// Initialize on Livewire navigation  
document.addEventListener('livewire:navigated', initUsersChart);
</script>
@endscript