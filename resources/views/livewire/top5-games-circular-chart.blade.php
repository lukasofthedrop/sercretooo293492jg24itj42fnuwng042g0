<div class="bg-black rounded-lg shadow-lg p-6 border border-gray-800">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-bold text-white">🎮 TOP 5 JOGOS MAIS JOGADOS</h3>
        <button 
            class="inline-flex items-center px-3 py-1.5 text-green-400 hover:text-green-300 hover:bg-green-400/10 text-sm font-medium transition-all duration-200 rounded-md border border-green-400/30 hover:border-green-400/60"
            onclick="window.showTop5Modal()"
        >
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            Ver Detalhes
        </button>
    </div>
    
    @if(count($chartData['labels']) > 0)
        <div class="relative h-80 flex justify-center">
            <canvas id="top5GamesChart" width="300" height="300"></canvas>
        </div>
        
        <!-- Lista responsiva -->
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-2">
            @foreach($chartData['labels'] as $index => $game)
                <div class="flex items-center justify-between text-sm p-2 sm:p-1 lg:p-0">
                    <div class="flex items-center min-w-0 flex-1">
                        <div class="w-3 h-3 rounded-full mr-2 flex-shrink-0" style="background-color: {{ $chartData['colors'][$index] ?? '#00ff7f' }}"></div>
                        <span class="text-white truncate text-xs sm:text-sm">{{ $game }}</span>
                    </div>
                    <div class="font-medium ml-2 flex-shrink-0 text-xs sm:text-sm" style="color: {{ $chartData['colors'][$index] ?? '#00ff41' }}">
                        {{ $chartData['data'][$index] }} apostas
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12 space-y-6">
            <!-- Ícone animado -->
            <div class="relative">
                <div class="text-6xl mb-4 opacity-60 animate-pulse">🎮</div>
                <div class="absolute -top-2 -right-2">
                    <div class="w-4 h-4 bg-green-400 rounded-full animate-bounce"></div>
                </div>
            </div>
            
            <!-- Título e mensagem -->
            <div class="space-y-2">
                <h4 class="text-xl font-bold text-white">Dashboard Aguardando Atividade</h4>
                <p class="text-gray-300 text-sm">Sistema inteligente pronto para análise</p>
            </div>
            
            <!-- Indicadores de status -->
            <div class="grid grid-cols-1 gap-3 max-w-xs mx-auto">
                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-400">Monitoramento</span>
                        <div class="flex space-x-1">
                            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse" style="animation-delay: 0.2s;"></div>
                            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse" style="animation-delay: 0.4s;"></div>
                        </div>
                    </div>
                    <p class="text-green-400 font-medium text-sm mt-1">🟢 Sistema Ativo</p>
                </div>
                
                <div class="bg-gray-800 rounded-lg p-3 border border-gray-700">
                    <div class="flex items-center justify-center space-x-2">
                        <span class="text-xs text-gray-300">Primeira aposta gerará análise automática</span>
                    </div>
                </div>
            </div>
            
            <!-- Call to action sutil -->
            <div class="pt-4">
                <div class="text-xs text-green-400 opacity-75 animate-pulse">
                    ✨ Analytics prontos para ativação
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Modal LUCRATIVA BET - PRETO E VERDE CORRIDA -->
<div id="top5Modal" class="fixed inset-0 bg-black bg-opacity-95 hidden z-50">
    <style>
        .lucrativa-modal-container {
            background: #000000;
            border: 2px solid #22c55e;
            box-shadow: 0 4px 25px rgba(34, 197, 94, 0.3);
            position: relative;
        }
        
        .lucrativa-modal-header {
            background: #000000;
            border-bottom: 2px solid #22c55e;
            position: relative;
        }
        
        .lucrativa-modal-title {
            color: #22c55e;
            font-weight: bold;
        }
        
        .lucrativa-modal-close {
            background: #000000;
            border: 2px solid #22c55e;
            color: #22c55e;
            transition: all 0.3s ease;
            padding: 8px 16px;
            border-radius: 4px;
        }
        
        .lucrativa-modal-close:hover {
            background: #22c55e;
            color: #000000;
        }
        
        .lucrativa-chart-section {
            background: #000000;
            border: 1px solid #22c55e;
            position: relative;
            padding: 20px;
            border-radius: 8px;
        }
        
        .lucrativa-table-section {
            background: #000000;
            border: 1px solid #22c55e;
            position: relative;
            padding: 20px;
            border-radius: 8px;
        }
        
        .lucrativa-section-title {
            color: #22c55e;
            font-weight: bold;
            border-bottom: 1px solid #22c55e;
            padding-bottom: 8px;
            margin-bottom: 16px;
        }
        
        .lucrativa-game-row {
            background: #000000;
            border: 1px solid #22c55e;
            transition: all 0.3s ease;
            position: relative;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 8px;
        }
        
        .lucrativa-game-row:hover {
            background: rgba(34, 197, 94, 0.05);
            border-color: #22c55e;
            transform: translateY(-2px);
        }
        
        .lucrativa-game-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        
        .lucrativa-game-name {
            color: #ffffff;
            font-weight: bold;
        }
        
        .lucrativa-game-stats {
            color: #22c55e;
            font-weight: bold;
        }
        
        @keyframes matrix-gaming-pulse {
            0% { opacity: 0.8; }
            100% { opacity: 1; }
        }
        
        @keyframes matrix-gaming-icons {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        @keyframes matrix-gaming-scan {
            0% { left: -100%; }
            100% { left: 100%; }
        }
        
        @keyframes matrix-pulse-indicator {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.8; }
        }
        
        .animate-matrix-games-in {
            animation: matrixGamesIn 0.5s ease-out forwards;
        }
        
        .animate-matrix-games-out {
            animation: matrixGamesOut 0.5s ease-in forwards;
        }
        
        @keyframes matrixGamesIn {
            from { 
                opacity: 0; 
                transform: scale(0.8) rotateY(15deg);
                filter: blur(8px);
            }
            to { 
                opacity: 1; 
                transform: scale(1) rotateY(0deg);
                filter: blur(0px);
            }
        }
        
        @keyframes matrixGamesOut {
            from { 
                opacity: 1; 
                transform: scale(1) rotateY(0deg);
                filter: blur(0px);
            }
            to { 
                opacity: 0; 
                transform: scale(0.8) rotateY(-15deg);
                filter: blur(8px);
            }
        }
    </style>
    
    <div class="flex items-center justify-center min-h-screen p-2 sm:p-4 relative z-10">
        <div class="matrix-games-container rounded-xl max-w-6xl w-full p-4 sm:p-6 max-h-[90vh] overflow-y-auto">
            <!-- Header Matrix Gaming -->
            <div class="matrix-games-header p-5 mb-6 rounded-t-xl">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="matrix-games-title text-2xl sm:text-3xl md:text-4xl font-black mb-3">
                            <span class="inline-block animate-bounce">🎮</span>
                            MATRIX GAMING ANALYTICS
                            <span class="inline-block animate-pulse">🔥</span>
                        </h2>
                        <div class="text-cyan-300 text-sm font-mono opacity-90 flex items-center space-x-2">
                            <span class="w-2 h-2 bg-cyan-400 rounded-full animate-pulse"></span>
                            <span>SISTEMA DE ANÁLISE DE JOGOS PROFISSIONAL</span>
                            <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                        </div>
                    </div>
                    <button onclick="window.closeTop5Modal()" class="matrix-games-close px-4 py-2 rounded-lg text-lg font-bold transition-all duration-300">
                        ✕ FECHAR
                    </button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Seção Gráfico Matrix -->
                <div class="matrix-chart-section rounded-xl p-5">
                    <h3 class="matrix-section-title text-lg font-bold mb-4 pb-2">
                        🏆 ANÁLISE CIRCULAR MATRIZ
                    </h3>
                    <div class="relative">
                        <canvas id="modalCircularChart" width="250" height="250" class="rounded-lg mx-auto"></canvas>
                        <div class="absolute bottom-2 left-2 right-2 text-center">
                            <div class="text-green-400 text-xs font-mono opacity-70 bg-black/50 rounded px-2 py-1">
                                MATRIZ: {{ count($chartData['labels']) }} CATEGORIAS ATIVAS
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Seção Dados Matrix -->
                <div class="matrix-table-section rounded-xl p-5">
                    <h3 class="matrix-section-title text-lg font-bold mb-4 pb-2">
                        📊 DADOS PROFISSIONAIS MATRIZ
                    </h3>
                    <div class="space-y-4 max-h-80 overflow-y-auto matrix-scrollbar">
                        @if(count($chartData['labels']) > 0)
                            @foreach($chartData['labels'] as $index => $game)
                                <div class="matrix-game-row rounded-lg p-4 relative">
                                    <div class="flex justify-between items-center relative z-10">
                                        <div class="flex items-center space-x-4">
                                            <div class="matrix-game-indicator w-4 h-4 rounded-full flex-shrink-0" 
                                                 style="background-color: {{ $chartData['colors'][$index] ?? '#00ff7f' }}"></div>
                                            <div class="min-w-0 flex-1">
                                                <div class="matrix-game-name text-sm font-bold">
                                                    {{ $game }}
                                                </div>
                                                <div class="text-green-300 text-xs font-mono mt-1">
                                                    CATEGORIA: JOGO #{!! $index + 1 !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <div class="matrix-game-stats text-lg" style="color: {{ $chartData['colors'][$index] ?? '#00ff41' }}">
                                                {{ $chartData['data'][$index] }} APOSTAS
                                            </div>
                                            <div class="text-cyan-300 text-sm font-mono">
                                                R$ {{ number_format($chartData['amounts'][$index] ?? 0, 2, ',', '.') }}
                                            </div>
                                            <div class="text-green-400 text-xs font-mono mt-1">
                                                RANK: #{{ $index + 1 }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-12">
                                <div class="text-4xl mb-4 animate-pulse">🎮</div>
                                <div class="text-white font-bold text-lg mb-2">SISTEMA GAMING MATRIZ STANDBY</div>
                                <div class="text-cyan-300 text-sm font-mono">Aguardando dados de jogos para análise...</div>
                                <div class="text-green-400 text-xs font-mono mt-2 opacity-60">
                                    SISTEMA PRONTO ● SENSORS ATIVOS ● WAITING FOR DATA
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Footer Matrix Gaming -->
            <div class="mt-6 pt-4 border-t border-cyan-400/30">
                <div class="flex justify-center items-center space-x-6 text-cyan-300 text-xs font-mono">
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                        <span>GAMING MATRIX</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 bg-cyan-400 rounded-full animate-pulse" style="animation-delay: 0.5s"></span>
                        <span>VERSÃO 3.0</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="w-2 h-2 bg-blue-400 rounded-full animate-pulse" style="animation-delay: 1s"></span>
                        <span>PROFESSIONAL ANALYTICS</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.matrix-scrollbar::-webkit-scrollbar {
    width: 10px;
}

.matrix-scrollbar::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.4);
    border-radius: 5px;
    border: 1px solid rgba(0, 255, 65, 0.2);
}

.matrix-scrollbar::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #00ff41, #00ccff);
    border-radius: 5px;
    box-shadow: 
        0 0 15px rgba(0, 255, 65, 0.5),
        inset 0 0 5px rgba(0, 0, 0, 0.3);
}

.matrix-scrollbar::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #00ccff, #00ff41);
    box-shadow: 
        0 0 20px rgba(0, 255, 65, 0.8),
        inset 0 0 5px rgba(0, 0, 0, 0.5);
}
</style>

@script
<script>
let chartInstance = null;
let modalChartInstance = null;

function initTop5Charts() {
    @if(count($chartData['labels']) == 0)
        return;
    @endif
    
    // Verificar se Chart.js está disponível
    if (typeof Chart === 'undefined') {
        console.log('Chart.js não disponível, aguardando...');
        setTimeout(initTop5Charts, 100);
        return;
    }
    
    const ctx = document.getElementById('top5GamesChart');
    if (ctx && !chartInstance) {
        chartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($chartData['labels']) !!},
                datasets: [{
                    data: {!! json_encode($chartData['data']) !!},
                    backgroundColor: {!! json_encode($chartData['colors']) !!},
                    borderColor: '#000000',
                    borderWidth: 3
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
    const modal = document.getElementById('top5Modal');
    modal.classList.remove('hidden');
    
    // Add Matrix Games animation class
    modal.classList.add('animate-matrix-games-in');
    
    // Setup modal close handlers
    setupModalCloseHandlers('top5Modal', window.closeTop5Modal);
    
    setTimeout(() => {
        const modalCtx = document.getElementById('modalCircularChart');
        if (modalCtx && !modalChartInstance) {
            // Verificar se Chart.js está disponível
            if (typeof Chart === 'undefined') {
                console.log('Chart.js não disponível para modal, carregando via CDN...');
                // Carregar Chart.js dinamicamente se não estiver disponível
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                script.onload = function() {
                    console.log('Chart.js carregado com sucesso');
                    createModalChart(modalCtx);
                };
                document.head.appendChild(script);
                return;
            }
            createModalChart(modalCtx);
        }
    }, 100);
}

function createModalChart(modalCtx) {
    modalChartInstance = new Chart(modalCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($chartData['labels']) !!},
            datasets: [{
                data: {!! json_encode($chartData['data']) !!},
                backgroundColor: {!! json_encode($chartData['colors']) !!},
                borderColor: '#000000',
                borderWidth: 3
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
                    backgroundColor: '#000000',
                    titleColor: '#ffffff',
                    bodyColor: '#00ff41',
                    borderColor: '#00ff41',
                    borderWidth: 2
                }
            },
            cutout: '40%'
        }
    });
}

// Helper function for modal close handlers
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

window.closeTop5Modal = function() {
    const modal = document.getElementById('top5Modal');
    modal.classList.add('animate-matrix-games-out');
    
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('animate-matrix-games-in', 'animate-matrix-games-out');
        
        if (modalChartInstance) {
            modalChartInstance.destroy();
            modalChartInstance = null;
        }
    }, 200);
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', initTop5Charts);

// Initialize on Livewire navigation
document.addEventListener('livewire:navigated', initTop5Charts);
</script>
@endscript