<div class="bg-black rounded-lg shadow-lg p-6 border border-gray-800">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-bold text-white">👑 RANKING PROFISSIONAL DE USUÁRIOS</h3>
        <button 
            class="inline-flex items-center px-3 py-1.5 text-green-400 hover:text-green-300 hover:bg-green-400/10 text-sm font-medium transition-all duration-200 rounded-md border border-green-400/30 hover:border-green-400/60"
            onclick="window.showUsersRankingModal()"
        >
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Ver Detalhes
        </button>
    </div>
    
    @if(count($chartData['labels']) > 0)
        <div class="relative h-80 mb-4">
            <canvas id="usersRankingChart"></canvas>
        </div>
        
        <!-- Grid responsivo de usuários -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-2 sm:gap-3">
            @foreach($chartData['labels'] as $index => $user)
                @if($index < 6)
                    <div class="flex items-center justify-between bg-gray-800 rounded-lg p-2 sm:p-3 min-w-0">
                        <div class="flex items-center min-w-0 flex-1">
                            <div class="text-xl sm:text-2xl mr-2 flex-shrink-0">
                                @if($index == 0) 👑
                                @elseif($index == 1) 🥇
                                @elseif($index == 2) 🥈
                                @elseif($index == 3) 🥉
                                @else ⭐
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-white font-medium text-xs sm:text-sm truncate">{{ $chartData['fullNames'][$index] ?? $user }}</div>
                                <div class="text-gray-400 text-xs">{{ $chartData['deposits'][$index] }} depósitos</div>
                            </div>
                        </div>
                        <div class="text-right ml-2 flex-shrink-0">
                            <div class="font-bold text-xs sm:text-sm" style="color: {{ $chartData['colors'][$index] ?? '#00ff41' }}">
                                <span class="hidden sm:inline">R$ </span>{{ number_format($chartData['amounts'][$index] ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <div class="text-center py-12 space-y-6">
            <!-- Ícone animado com efeitos -->
            <div class="relative">
                <div class="text-6xl mb-4 opacity-70 animate-pulse">👑</div>
                <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2">
                    <div class="flex space-x-1">
                        <div class="w-2 h-6 bg-green-400 rounded-sm animate-pulse" style="animation-delay: 0s;"></div>
                        <div class="w-2 h-4 bg-blue-400 rounded-sm animate-pulse" style="animation-delay: 0.2s;"></div>
                        <div class="w-2 h-8 bg-orange-400 rounded-sm animate-pulse" style="animation-delay: 0.4s;"></div>
                        <div class="w-2 h-3 bg-yellow-400 rounded-sm animate-pulse" style="animation-delay: 0.6s;"></div>
                        <div class="w-2 h-5 bg-pink-400 rounded-sm animate-pulse" style="animation-delay: 0.8s;"></div>
                    </div>
                </div>
            </div>
            
            <!-- Título e descrição -->
            <div class="space-y-3">
                <h4 class="text-xl font-bold text-white">Ranking Profissional Preparado</h4>
                <p class="text-gray-300 text-sm max-w-sm mx-auto">Sistema de ranqueamento inteligente aguardando primeiros depósitos para análise competitiva</p>
            </div>
            
            <!-- Medalhas placeholder -->
            <div class="flex justify-center space-x-4 py-4">
                <div class="text-center space-y-1">
                    <div class="text-2xl opacity-50 grayscale">🥇</div>
                    <div class="text-xs text-gray-500">1º Lugar</div>
                </div>
                <div class="text-center space-y-1">
                    <div class="text-2xl opacity-50 grayscale">🥈</div>
                    <div class="text-xs text-gray-500">2º Lugar</div>
                </div>
                <div class="text-center space-y-1">
                    <div class="text-2xl opacity-50 grayscale">🥉</div>
                    <div class="text-xs text-gray-500">3º Lugar</div>
                </div>
            </div>
            
            <!-- Status info -->
            <div class="bg-gray-800 rounded-lg p-4 mx-auto max-w-sm border border-gray-700">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-400">Status do Ranking</span>
                    <div class="flex items-center space-x-1">
                        <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-green-400 text-xs">Standby</span>
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-white font-medium text-sm">🚀 Primeiro depósito ativará competição</p>
                    <p class="text-xs text-gray-400 mt-1">Ranking automático por valor depositado</p>
                </div>
            </div>
            
            <!-- Call to action -->
            <div class="pt-2">
                <div class="text-xs text-green-400 opacity-75 animate-pulse">
                    💎 Sistema VIP aguardando ativação
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Modal MATRIX PROFISSIONAL para ranking completo -->
<div id="usersRankingModal" class="fixed inset-0 bg-black bg-opacity-95 hidden z-50 matrix-modal-backdrop">
    <style>
        .matrix-modal-backdrop::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(ellipse at center, rgba(0, 255, 65, 0.1) 0%, rgba(0, 0, 0, 0.9) 70%);
            animation: matrix-pulse 3s ease-in-out infinite alternate;
        }
        
        .matrix-container {
            background: linear-gradient(135deg, #000000 0%, #001100 50%, #000000 100%);
            border: 2px solid #00ff41;
            box-shadow: 
                0 0 20px rgba(0, 255, 65, 0.5),
                inset 0 0 20px rgba(0, 255, 65, 0.1),
                0 0 40px rgba(0, 255, 65, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .matrix-container::before {
            content: '0101010 1010101 0101010 1010101 0101010';
            position: absolute;
            top: -50px;
            left: 0;
            right: 0;
            color: rgba(0, 255, 65, 0.1);
            font-family: 'Courier New', monospace;
            font-size: 12px;
            letter-spacing: 2px;
            animation: matrix-rain 15s linear infinite;
            white-space: nowrap;
            overflow: hidden;
        }
        
        .matrix-header {
            background: linear-gradient(90deg, rgba(0, 255, 65, 0.2) 0%, rgba(0, 255, 65, 0.1) 100%);
            border-bottom: 1px solid #00ff41;
            position: relative;
        }
        
        .matrix-title {
            color: #00ff41;
            text-shadow: 0 0 10px rgba(0, 255, 65, 0.8);
            font-family: 'Courier New', monospace;
            letter-spacing: 1px;
        }
        
        .matrix-close {
            background: rgba(0, 255, 65, 0.1);
            border: 1px solid #00ff41;
            color: #00ff41;
            text-shadow: 0 0 10px rgba(0, 255, 65, 0.8);
            transition: all 0.3s ease;
        }
        
        .matrix-close:hover {
            background: rgba(0, 255, 65, 0.2);
            box-shadow: 0 0 15px rgba(0, 255, 65, 0.6);
            transform: scale(1.1);
        }
        
        .matrix-section {
            background: linear-gradient(145deg, rgba(0, 20, 0, 0.8) 0%, rgba(0, 0, 0, 0.9) 100%);
            border: 1px solid rgba(0, 255, 65, 0.3);
            box-shadow: inset 0 0 15px rgba(0, 255, 65, 0.1);
            position: relative;
        }
        
        .matrix-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, #00ff41 50%, transparent 100%);
            animation: matrix-scan 2s linear infinite;
        }
        
        .matrix-section-title {
            color: #00ff41;
            text-shadow: 0 0 8px rgba(0, 255, 65, 0.6);
            font-family: 'Courier New', monospace;
            border-bottom: 1px solid rgba(0, 255, 65, 0.3);
        }
        
        .matrix-user-row {
            background: linear-gradient(90deg, rgba(0, 255, 65, 0.05) 0%, transparent 50%, rgba(0, 255, 65, 0.05) 100%);
            border: 1px solid rgba(0, 255, 65, 0.2);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .matrix-user-row::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent 0%, rgba(0, 255, 65, 0.2) 50%, transparent 100%);
            transition: left 0.5s ease;
        }
        
        .matrix-user-row:hover {
            background: linear-gradient(90deg, rgba(0, 255, 65, 0.1) 0%, rgba(0, 255, 65, 0.05) 50%, rgba(0, 255, 65, 0.1) 100%);
            border-color: #00ff41;
            box-shadow: 0 0 15px rgba(0, 255, 65, 0.3);
            transform: translateY(-2px);
        }
        
        .matrix-user-row:hover::before {
            left: 100%;
        }
        
        .matrix-rank-badge {
            background: radial-gradient(circle, #00ff41 0%, #00cc33 100%);
            color: #000;
            font-weight: bold;
            text-shadow: none;
            box-shadow: 0 0 10px rgba(0, 255, 65, 0.6);
        }
        
        .matrix-amount {
            text-shadow: 0 0 8px currentColor;
            font-family: 'Courier New', monospace;
            font-weight: bold;
        }
        
        @keyframes matrix-pulse {
            0% { opacity: 0.8; }
            100% { opacity: 1; }
        }
        
        @keyframes matrix-rain {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        @keyframes matrix-scan {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .animate-matrix-fade-in {
            animation: matrixFadeIn 0.4s ease-out forwards;
        }
        
        .animate-matrix-fade-out {
            animation: matrixFadeOut 0.4s ease-in forwards;
        }
        
        @keyframes matrixFadeIn {
            from { 
                opacity: 0; 
                transform: scale(0.9) rotateX(10deg);
                filter: blur(5px);
            }
            to { 
                opacity: 1; 
                transform: scale(1) rotateX(0deg);
                filter: blur(0px);
            }
        }
        
        @keyframes matrixFadeOut {
            from { 
                opacity: 1; 
                transform: scale(1) rotateX(0deg);
                filter: blur(0px);
            }
            to { 
                opacity: 0; 
                transform: scale(0.9) rotateX(-10deg);
                filter: blur(5px);
            }
        }
    </style>
    
    <div class="flex items-center justify-center min-h-screen p-2 sm:p-4 relative z-10">
        <div class="matrix-container rounded-lg max-w-7xl w-full p-4 sm:p-6 max-h-[90vh] overflow-y-auto">
            <!-- Header Matrix -->
            <div class="matrix-header p-4 mb-6 rounded-t-lg">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="matrix-title text-xl sm:text-2xl md:text-3xl font-bold mb-2">
                            <span class="inline-block animate-pulse">👑</span>
                            MATRIX RANKING SYSTEM
                        </h2>
                        <div class="text-green-300 text-sm font-mono opacity-80">
                            <span class="animate-pulse">●</span> SISTEMA DE ANÁLISE PROFISSIONAL ATIVO
                        </div>
                    </div>
                    <button onclick="window.closeUsersRankingModal()" class="matrix-close px-4 py-2 rounded-lg text-lg font-bold transition-all duration-300">
                        ✕ SAIR
                    </button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                <!-- Seção Gráfico Matrix -->
                <div class="matrix-section rounded-lg p-5">
                    <h3 class="matrix-section-title text-lg font-bold mb-4 pb-2">
                        📊 ANÁLISE GRÁFICA MATRIZ
                    </h3>
                    <div class="h-80 relative">
                        <canvas id="modalColumnChart" class="rounded-lg"></canvas>
                        <div class="absolute bottom-2 right-2 text-green-400 text-xs font-mono opacity-60">
                            MATRIZ: {{ count($chartData['labels']) }} USUÁRIOS
                        </div>
                    </div>
                </div>
                
                <!-- Seção Tabela Matrix -->
                <div class="matrix-section rounded-lg p-5">
                    <h3 class="matrix-section-title text-lg font-bold mb-4 pb-2">
                        💰 RANKING DETALHADO MATRIZ
                    </h3>
                    <div class="space-y-3 max-h-80 overflow-y-auto custom-scrollbar">
                        @if(count($chartData['labels']) > 0)
                            @foreach($chartData['labels'] as $index => $user)
                                <div class="matrix-user-row rounded-lg p-3 relative">
                                    <div class="flex items-center justify-between relative z-10">
                                        <div class="flex items-center space-x-4">
                                            <div class="flex-shrink-0">
                                                @if($index == 0)
                                                    <div class="text-2xl animate-pulse">👑</div>
                                                @elseif($index == 1)
                                                    <div class="text-xl">🥇</div>
                                                @elseif($index == 2)
                                                    <div class="text-xl">🥈</div>
                                                @elseif($index == 3)
                                                    <div class="text-xl">🥉</div>
                                                @else
                                                    <span class="matrix-rank-badge rounded-full w-7 h-7 flex items-center justify-center text-xs">
                                                        {{ $index + 1 }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="text-white font-bold text-sm">
                                                    {{ $chartData['fullNames'][$index] ?? $user }}
                                                </div>
                                                <div class="text-green-300 text-xs font-mono">
                                                    {{ $chartData['emails'][$index] ?? 'matrix@system.com' }}
                                                </div>
                                                <div class="text-green-400 text-xs mt-1">
                                                    DEPÓSITOS: {{ $chartData['deposits'][$index] }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <div class="matrix-amount text-lg" style="color: {{ $chartData['colors'][$index] ?? '#00ff41' }}">
                                                R$ {{ number_format($chartData['amounts'][$index] ?? 0, 2, ',', '.') }}
                                            </div>
                                            <div class="text-green-300 text-xs font-mono">
                                                POSIÇÃO: #{{ $index + 1 }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <div class="text-green-400 text-xl mb-2">⚡</div>
                                <div class="text-white font-bold">SISTEMA MATRIZ STANDBY</div>
                                <div class="text-green-300 text-sm font-mono">Aguardando dados para análise...</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Footer Matrix -->
            <div class="mt-6 pt-4 border-t border-green-400/30">
                <div class="flex justify-center items-center space-x-4 text-green-400 text-xs font-mono">
                    <span class="animate-pulse">●</span>
                    <span>SISTEMA MATRIZ VERSÃO 2.1</span>
                    <span>●</span>
                    <span>ANÁLISE PROFISSIONAL ATIVA</span>
                    <span class="animate-pulse">●</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.3);
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #00ff41, #00cc33);
    border-radius: 4px;
    box-shadow: 0 0 10px rgba(0, 255, 65, 0.5);
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #00cc33, #00ff41);
    box-shadow: 0 0 15px rgba(0, 255, 65, 0.8);
}
</style>

@script
<script>
let usersChartInstance = null;
let modalUsersChartInstance = null;

function initUsersChart() {
    @if(count($chartData['labels']) == 0)
        return;
    @endif
    
    // Verificar se Chart.js está disponível
    if (typeof Chart === 'undefined') {
        console.log('Chart.js não disponível, aguardando...');
        setTimeout(initUsersChart, 100);
        return;
    }
    
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
    const modal = document.getElementById('usersRankingModal');
    modal.classList.remove('hidden');
    
    // Add Matrix animation class
    modal.classList.add('animate-matrix-fade-in');
    
    // Setup modal close handlers (using same helper function)
    setupModalCloseHandlers('usersRankingModal', window.closeUsersRankingModal);
    
    setTimeout(() => {
        const modalCtx = document.getElementById('modalColumnChart');
        if (modalCtx && !modalUsersChartInstance) {
            // Verificar se Chart.js está disponível
            if (typeof Chart === 'undefined') {
                console.log('Chart.js não disponível para modal, carregando via CDN...');
                // Carregar Chart.js dinamicamente se não estiver disponível
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                script.onload = function() {
                    console.log('Chart.js carregado com sucesso');
                    createModalUsersChart(modalCtx);
                };
                document.head.appendChild(script);
                return;
            }
            createModalUsersChart(modalCtx);
        }
    }, 100);
}

function createModalUsersChart(modalCtx) {
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

// Helper function for modal close handlers (shared)
function setupModalCloseHandlers(modalId, closeFunction) {
    const modal = document.getElementById(modalId);
    
    // Close on ESC key
    const escHandler = function(event) {
        if (event.key === 'Escape') {
            closeFunction();
            document.removeEventListener('keydown', escHandler);
        }
    };
    document.addEventListener('keydown', escHandler);
    
    // Close on backdrop click
    const backdropHandler = function(event) {
        if (event.target === modal) {
            closeFunction();
            modal.removeEventListener('click', backdropHandler);
        }
    };
    modal.addEventListener('click', backdropHandler);
}

window.closeUsersRankingModal = function() {
    const modal = document.getElementById('usersRankingModal');
    modal.classList.add('animate-matrix-fade-out');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('animate-matrix-fade-in', 'animate-matrix-fade-out');
        
        if (modalUsersChartInstance) {
            modalUsersChartInstance.destroy();
            modalUsersChartInstance = null;
        }
    }, 200);
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', initUsersChart);

// Initialize on Livewire navigation  
document.addEventListener('livewire:navigated', initUsersChart);
</script>
@endscript