<x-filament-widgets::widget>
    <x-filament::section>
        <!-- Filtros de Período -->
        <div class="mb-6 flex justify-center gap-2 flex-wrap">
            <button onclick="loadData('today')" class="filter-btn px-4 py-2 bg-green-500/20 text-green-400 rounded-lg border border-green-500/30 hover:bg-green-500/30 transition-all duration-300">
                📅 HOJE
            </button>
            <button onclick="loadData('week')" class="filter-btn px-4 py-2 bg-gray-800/50 text-gray-400 rounded-lg border border-gray-600/30 hover:bg-green-500/30 hover:text-green-400 hover:border-green-500/30 transition-all duration-300">
                📊 ÚLTIMA SEMANA  
            </button>
            <button onclick="loadData('month')" class="filter-btn px-4 py-2 bg-gray-800/50 text-gray-400 rounded-lg border border-gray-600/30 hover:bg-green-500/30 hover:text-green-400 hover:border-green-500/30 transition-all duration-300">
                📈 ÚLTIMO MÊS
            </button>
        </div>
        
        <!-- Cards de Estatísticas Rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-green-500/20 to-green-700/20 rounded-lg p-4 border border-green-500/30">
                <div class="text-green-400 text-sm font-semibold">💰 TOTAL DEPÓSITOS</div>
                <div class="text-white text-2xl font-bold mt-2" id="total-deposits">R$ 0,00</div>
            </div>
            <div class="bg-gradient-to-br from-blue-500/20 to-blue-700/20 rounded-lg p-4 border border-blue-500/30">
                <div class="text-blue-400 text-sm font-semibold">👥 NOVOS USUÁRIOS</div>
                <div class="text-white text-2xl font-bold mt-2" id="total-users">0</div>
            </div>
            <div class="bg-gradient-to-br from-yellow-500/20 to-yellow-700/20 rounded-lg p-4 border border-yellow-500/30">
                <div class="text-yellow-400 text-sm font-semibold">🎮 TOTAL APOSTAS</div>
                <div class="text-white text-2xl font-bold mt-2" id="total-bets">0</div>
            </div>
            <div class="bg-gradient-to-br from-purple-500/20 to-purple-700/20 rounded-lg p-4 border border-purple-500/30">
                <div class="text-purple-400 text-sm font-semibold">📊 LUCRO LÍQUIDO</div>
                <div class="text-white text-2xl font-bold mt-2" id="total-profit">R$ 0,00</div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Gráfico de Depósitos -->
            <div class="bg-black/50 rounded-lg p-4 border border-green-500/30">
                <h3 class="text-green-400 font-bold mb-4">📈 DEPÓSITOS</h3>
                <div id="chart-deposits" style="min-height: 350px;"></div>
            </div>
            
            <!-- Gráfico de Usuários -->
            <div class="bg-black/50 rounded-lg p-4 border border-green-500/30">
                <h3 class="text-green-400 font-bold mb-4">👥 NOVOS USUÁRIOS</h3>
                <div id="chart-users" style="min-height: 350px;"></div>
            </div>
            
            <!-- Gráfico de Jogos -->
            <div class="bg-black/50 rounded-lg p-4 border border-green-500/30">
                <h3 class="text-green-400 font-bold mb-4">🎮 TOP JOGOS</h3>
                <div id="chart-games" style="min-height: 380px;"></div>
            </div>
            
            <!-- Gráfico de Receita -->
            <div class="bg-black/50 rounded-lg p-4 border border-green-500/30">
                <h3 class="text-green-400 font-bold mb-4">💰 RECEITA VS LUCRO</h3>
                <div id="chart-revenue" style="min-height: 350px;"></div>
            </div>
        </div>
        
        <!-- Carregar ApexCharts CDN -->
        <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.min.js"></script>
        
        <script>
        // Variáveis globais
        let charts = {};
        
        // Função para carregar dados
        function loadData(period = 'today') {
            console.log('Carregando dados para:', period);
            
            fetch(`/api/admin/dashboard-metrics?period=${period}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Dados recebidos:', data);
                updateDashboard(data);
            })
            .catch(error => {
                console.error('Erro ao carregar dados:', error);
            });
        }
        
        // Função para atualizar dashboard
        function updateDashboard(data) {
            // Atualizar estatísticas
            if (data.stats) {
                document.getElementById('total-deposits').textContent = 
                    'R$ ' + parseFloat(data.stats.total_deposits).toLocaleString('pt-BR', {minimumFractionDigits: 2});
                document.getElementById('total-users').textContent = data.stats.total_users;
                document.getElementById('total-bets').textContent = data.stats.total_bets;
                document.getElementById('total-profit').textContent = 
                    'R$ ' + parseFloat(data.stats.total_profit).toLocaleString('pt-BR', {minimumFractionDigits: 2});
            }
            
            // Destruir gráficos existentes
            Object.values(charts).forEach(chart => {
                if (chart && typeof chart.destroy === 'function') {
                    chart.destroy();
                }
            });
            
            // Criar gráfico de depósitos
            if (data.deposits && data.deposits.length > 0) {
                charts.deposits = new ApexCharts(document.querySelector("#chart-deposits"), {
                    series: [{
                        name: 'Depósitos',
                        data: data.deposits
                    }],
                    chart: {
                        type: 'area',
                        height: 350,
                        background: 'transparent',
                        toolbar: { show: false }
                    },
                    colors: ['#22c55e'],
                    stroke: { curve: 'smooth', width: 2 },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.5,
                            opacityTo: 0.1
                        }
                    },
                    xaxis: {
                        type: 'datetime',
                        labels: { style: { colors: '#9ca3af' } }
                    },
                    yaxis: {
                        labels: {
                            style: { colors: '#9ca3af' },
                            formatter: (val) => 'R$ ' + val
                        }
                    },
                    theme: { mode: 'dark' },
                    grid: { borderColor: '#374151' }
                });
                charts.deposits.render();
            }
            
            // Criar gráfico de usuários
            if (data.users && data.users.data) {
                charts.users = new ApexCharts(document.querySelector("#chart-users"), {
                    series: [{
                        name: 'Usuários',
                        data: data.users.data
                    }],
                    chart: {
                        type: 'line',
                        height: 350,
                        background: 'transparent',
                        toolbar: { show: false }
                    },
                    colors: ['#3b82f6'],
                    stroke: { curve: 'smooth', width: 3 },
                    xaxis: {
                        categories: data.users.labels,
                        labels: { style: { colors: '#9ca3af' } }
                    },
                    yaxis: {
                        labels: { style: { colors: '#9ca3af' } }
                    },
                    theme: { mode: 'dark' },
                    grid: { borderColor: '#374151' }
                });
                charts.users.render();
            }
            
            // Criar gráfico de jogos
            if (data.games && data.games.data) {
                charts.games = new ApexCharts(document.querySelector("#chart-games"), {
                    series: data.games.data,
                    labels: data.games.labels,
                    chart: {
                        type: 'donut',
                        height: 380,
                        background: 'transparent'
                    },
                    colors: ['#22c55e', '#3b82f6', '#f59e0b', '#ef4444', '#a855f7'],
                    theme: { mode: 'dark' },
                    legend: {
                        position: 'bottom',
                        labels: { colors: '#ffffff' }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '60%'
                            }
                        }
                    }
                });
                charts.games.render();
            }
            
            // Criar gráfico de receita
            if (data.revenue && data.revenue.receita) {
                charts.revenue = new ApexCharts(document.querySelector("#chart-revenue"), {
                    series: [{
                        name: 'Receita',
                        data: data.revenue.receita
                    }, {
                        name: 'Lucro',
                        data: data.revenue.lucro
                    }],
                    chart: {
                        type: 'bar',
                        height: 350,
                        background: 'transparent',
                        toolbar: { show: false }
                    },
                    colors: ['#22c55e', '#a855f7'],
                    plotOptions: {
                        bar: {
                            columnWidth: '50%',
                            borderRadius: 4
                        }
                    },
                    xaxis: {
                        categories: data.revenue.labels,
                        labels: { style: { colors: '#9ca3af' } }
                    },
                    yaxis: {
                        labels: {
                            style: { colors: '#9ca3af' },
                            formatter: (val) => 'R$ ' + val
                        }
                    },
                    theme: { mode: 'dark' },
                    grid: { borderColor: '#374151' }
                });
                charts.revenue.render();
            }
        }
        
        // Carregar dados ao iniciar
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                loadData('month');
            }, 500);
        });
        </script>
    </x-filament::section>
</x-filament-widgets::widget>