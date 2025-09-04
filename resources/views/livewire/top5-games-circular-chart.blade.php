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

<!-- Modal MATRIX PROFISSIONAL para Top 5 Games -->
<div id="top5Modal" class="fixed inset-0 bg-black bg-opacity-95 hidden z-50 matrix-games-backdrop">
    <style>
        .matrix-games-backdrop::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 30%, rgba(0, 255, 65, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(0, 200, 255, 0.08) 0%, transparent 50%),
                linear-gradient(135deg, rgba(0, 0, 0, 0.9) 0%, rgba(0, 20, 0, 0.95) 100%);
            animation: matrix-gaming-pulse 4s ease-in-out infinite alternate;
        }
        
        .matrix-games-container {
            background: linear-gradient(135deg, #000000 0%, #001a00 30%, #000a00 70%, #000000 100%);
            border: 2px solid #00ff41;
            box-shadow: 
                0 0 30px rgba(0, 255, 65, 0.4),
                inset 0 0 30px rgba(0, 255, 65, 0.1),
                0 0 60px rgba(0, 255, 65, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .matrix-games-container::before {
            content: '🎮 🔥 ⚡ 🏆 📊 🎮 🔥 ⚡ 🏆 📊';
            position: absolute;
            top: -30px;
            left: 0;
            right: 0;
            color: rgba(0, 255, 65, 0.15);
            font-size: 16px;
            animation: matrix-gaming-icons 20s linear infinite;
            white-space: nowrap;
            overflow: hidden;
        }
        
        .matrix-games-header {
            background: linear-gradient(90deg, rgba(0, 255, 65, 0.15) 0%, rgba(0, 200, 255, 0.1) 100%);
            border-bottom: 2px solid #00ff41;
            position: relative;
        }
        
        .matrix-games-title {
            background: linear-gradient(45deg, #00ff41, #00ccff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 0 20px rgba(0, 255, 65, 0.5);
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
        }
        
        .matrix-games-close {
            background: linear-gradient(135deg, rgba(255, 0, 100, 0.2), rgba(0, 255, 65, 0.1));
            border: 2px solid #ff0066;
            color: #ff0066;
            text-shadow: 0 0 10px rgba(255, 0, 100, 0.8);
            transition: all 0.3s ease;
        }
        
        .matrix-games-close:hover {
            background: linear-gradient(135deg, rgba(255, 0, 100, 0.3), rgba(0, 255, 65, 0.2));
            box-shadow: 0 0 20px rgba(255, 0, 100, 0.6);
            transform: scale(1.1) rotate(5deg);
        }
        
        .matrix-chart-section {
            background: linear-gradient(145deg, rgba(0, 30, 0, 0.8) 0%, rgba(0, 10, 20, 0.9) 100%);
            border: 2px solid rgba(0, 255, 65, 0.3);
            box-shadow: 
                inset 0 0 20px rgba(0, 255, 65, 0.1),
                0 0 15px rgba(0, 255, 65, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .matrix-chart-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent 0%, #00ff41 50%, transparent 100%);
            animation: matrix-gaming-scan 3s linear infinite;
        }
        
        .matrix-table-section {
            background: linear-gradient(145deg, rgba(0, 20, 30, 0.8) 0%, rgba(0, 0, 20, 0.9) 100%);
            border: 2px solid rgba(0, 200, 255, 0.3);
            box-shadow: 
                inset 0 0 20px rgba(0, 200, 255, 0.1),
                0 0 15px rgba(0, 200, 255, 0.2);
            position: relative;
        }
        
        .matrix-section-title {
            background: linear-gradient(45deg, #00ff41, #00ccff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-family: 'Courier New', monospace;
            border-bottom: 1px solid rgba(0, 255, 65, 0.4);
            text-shadow: 0 0 10px rgba(0, 255, 65, 0.3);
        }
        
        .matrix-game-row {
            background: linear-gradient(90deg, 
                rgba(0, 255, 65, 0.05) 0%, 
                transparent 30%, 
                rgba(0, 200, 255, 0.05) 70%,
                transparent 100%
            );
            border: 1px solid rgba(0, 255, 65, 0.2);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
        }
        
        .matrix-game-row::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent 0%, 
                rgba(0, 255, 65, 0.1) 20%,
                rgba(0, 200, 255, 0.1) 80%,
                transparent 100%
            );
            transition: left 0.6s ease;
        }
        
        .matrix-game-row:hover {
            background: linear-gradient(90deg, 
                rgba(0, 255, 65, 0.15) 0%, 
                rgba(0, 200, 255, 0.1) 50%,
                rgba(0, 255, 65, 0.15) 100%
            );
            border-color: #00ff41;
            box-shadow: 
                0 0 20px rgba(0, 255, 65, 0.3),
                inset 0 0 10px rgba(0, 255, 65, 0.1);
            transform: translateY(-3px) scale(1.02);
        }
        
        .matrix-game-row:hover::before {
            left: 100%;
        }
        
        .matrix-game-indicator {
            box-shadow: 0 0 15px currentColor;
            animation: matrix-pulse-indicator 2s ease-in-out infinite;
        }
        
        .matrix-game-name {
            color: #ffffff;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            text-shadow: 0 0 8px rgba(255, 255, 255, 0.3);
        }
        
        .matrix-game-stats {
            font-family: 'Courier New', monospace;
            font-weight: bold;
            text-shadow: 0 0 10px currentColor;
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
    }, 100);
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