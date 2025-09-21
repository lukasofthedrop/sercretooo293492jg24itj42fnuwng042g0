<x-filament-panels::page>
    @if(request()->has('affiliate_id'))
        @php
            $data = $this->getAnalyticsData(request()->get('affiliate_id'));
        @endphp
        
        @if(!empty($data))
            <style>
                .info-card {
                    background: white;
                    border-radius: 12px;
                    padding: 20px;
                    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
                }
                .stat-box {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    border-radius: 8px;
                    padding: 15px;
                    color: white;
                }
                .metric-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 1rem;
                }
            </style>

            {{-- Cabeçalho do Afiliado --}}
            <div class="info-card mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold">{{ $data['affiliate']['name'] }}</h2>
                        <p class="text-gray-600">{{ $data['affiliate']['email'] }}</p>
                        <p class="text-sm text-gray-500 mt-1">
                            Código: <code class="bg-gray-100 px-2 py-1 rounded">{{ $data['affiliate']['code'] }}</code>
                            | Cadastro: {{ $data['affiliate']['created_at'] }}
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-sm text-gray-500">Tier:</span>
                        <div class="text-lg font-bold">
                            @switch($data['settings']['tier'])
                                @case('bronze')
                                    <span style="color: #CD7F32">🥉 Bronze</span>
                                    @break
                                @case('silver')
                                    <span style="color: #C0C0C0">🥈 Prata</span>
                                    @break
                                @case('gold')
                                    <span style="color: #FFD700">🥇 Ouro</span>
                                    @break
                                @case('custom')
                                    <span style="color: #8B008B">💎 Custom</span>
                                    @break
                            @endswitch
                        </div>
                        <div class="mt-2">
                            @if($data['settings']['is_active'])
                                <span class="text-green-600 font-semibold">✅ Ativo</span>
                            @else
                                <span class="text-red-600 font-semibold">❌ Inativo</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Configurações de Comissão --}}
            <div class="info-card mb-6">
                <h3 class="text-lg font-semibold mb-4">⚙️ Configurações de Comissão</h3>
                <div class="metric-grid">
                    <div class="stat-box">
                        <div class="text-sm opacity-90">RevShare Display (FAKE)</div>
                        <div class="text-2xl font-bold">{{ $data['settings']['revshare_display'] }}%</div>
                        <div class="text-xs opacity-75">O que o afiliado vê</div>
                    </div>
                    <div class="stat-box" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <div class="text-sm opacity-90">RevShare Real</div>
                        <div class="text-2xl font-bold">{{ $data['settings']['revshare_real'] }}%</div>
                        <div class="text-xs opacity-75">Valor aplicado</div>
                    </div>
                    <div class="stat-box" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="text-sm opacity-90">CPA</div>
                        <div class="text-2xl font-bold">R$ {{ number_format($data['settings']['cpa_value'], 2, ',', '.') }}</div>
                        <div class="text-xs opacity-75">Por conversão</div>
                    </div>
                    <div class="stat-box" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        <div class="text-sm opacity-90">NGR Mínimo</div>
                        <div class="text-2xl font-bold">R$ {{ number_format($data['settings']['ngr_minimum'], 2, ',', '.') }}</div>
                        <div class="text-xs opacity-75">Para qualificar</div>
                    </div>
                </div>
            </div>

            {{-- Métricas Gerais --}}
            <div class="info-card mb-6">
                <h3 class="text-lg font-semibold mb-4">📊 Métricas de Performance</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="border rounded-lg p-4">
                        <div class="text-sm text-gray-600">Total de Indicados</div>
                        <div class="text-3xl font-bold text-blue-600">{{ $data['metrics']['total_referred'] }}</div>
                        <div class="text-sm text-gray-500">{{ $data['metrics']['active_referred'] }} ativos</div>
                    </div>
                    <div class="border rounded-lg p-4">
                        <div class="text-sm text-gray-600">Taxa de Conversão</div>
                        <div class="text-3xl font-bold text-green-600">{{ $data['metrics']['conversion_rate'] }}%</div>
                        <div class="text-sm text-gray-500">Ativos / Total</div>
                    </div>
                    <div class="border rounded-lg p-4">
                        <div class="text-sm text-gray-600">Comissões Totais</div>
                        <div class="text-3xl font-bold text-purple-600">R$ {{ number_format($data['metrics']['total_commissions'], 2, ',', '.') }}</div>
                        <div class="text-sm text-gray-500">R$ {{ number_format($data['metrics']['pending_commissions'], 2, ',', '.') }} pendente</div>
                    </div>
                </div>
            </div>

            {{-- NGR Detalhado --}}
            <div class="info-card mb-6">
                <h3 class="text-lg font-semibold mb-4">💰 NGR - Net Gaming Revenue</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="border rounded-lg p-4">
                        <div class="text-sm text-gray-600">Depósitos</div>
                        <div class="text-2xl font-bold text-green-600">
                            R$ {{ number_format($data['metrics']['ngr']['total_deposits'], 2, ',', '.') }}
                        </div>
                    </div>
                    <div class="border rounded-lg p-4">
                        <div class="text-sm text-gray-600">Saques</div>
                        <div class="text-2xl font-bold text-red-600">
                            R$ {{ number_format($data['metrics']['ngr']['total_withdrawals'], 2, ',', '.') }}
                        </div>
                    </div>
                    <div class="border rounded-lg p-4">
                        <div class="text-sm text-gray-600">NGR Total</div>
                        <div class="text-2xl font-bold {{ $data['metrics']['ngr']['ngr'] >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                            R$ {{ number_format($data['metrics']['ngr']['ngr'], 2, ',', '.') }}
                        </div>
                    </div>
                    <div class="border rounded-lg p-4">
                        <div class="text-sm text-gray-600">Status</div>
                        <div class="text-2xl font-bold">
                            @if($data['metrics']['ngr']['qualified'])
                                <span class="text-green-600">✅ Qualificado</span>
                            @else
                                <span class="text-red-600">❌ Não Qualificado</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="mt-4 p-3 bg-gray-50 rounded">
                    <p class="text-sm text-gray-600">
                        Período: {{ $data['metrics']['ngr']['period'] }} | 
                        {{ $data['metrics']['ngr']['start_date'] }} até {{ $data['metrics']['ngr']['end_date'] }}
                    </p>
                </div>
            </div>

            {{-- Gráfico dos Últimos 30 Dias --}}
            <div class="info-card mb-6">
                <h3 class="text-lg font-semibold mb-4">📈 Últimos 30 Dias</h3>
                <canvas id="last30DaysChart" height="100"></canvas>
            </div>

            {{-- Top Indicados --}}
            <div class="info-card">
                <h3 class="text-lg font-semibold mb-4">🏆 Top 10 Indicados</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left">Nome</th>
                                <th class="px-4 py-2 text-center">Depósitos</th>
                                <th class="px-4 py-2 text-center">Saques</th>
                                <th class="px-4 py-2 text-center">NGR</th>
                                <th class="px-4 py-2 text-center">Cadastro</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['top_referred'] as $referred)
                            <tr class="border-b">
                                <td class="px-4 py-2">
                                    <div>
                                        <div class="font-medium">{{ $referred['name'] }}</div>
                                        <div class="text-sm text-gray-500">{{ $referred['email'] }}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-2 text-center text-green-600">
                                    R$ {{ number_format($referred['deposits'], 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-2 text-center text-red-600">
                                    R$ {{ number_format($referred['withdrawals'], 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-2 text-center font-semibold">
                                    R$ {{ number_format($referred['ngr'], 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-2 text-center text-sm">
                                    {{ $referred['created_at'] }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const ctx = document.getElementById('last30DaysChart').getContext('2d');
                const chartData = @json($data['last_30_days']);
                
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.map(d => d.date),
                        datasets: [
                            {
                                label: 'Depósitos',
                                data: chartData.map(d => d.deposits),
                                borderColor: 'rgb(34, 197, 94)',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                tension: 0.3
                            },
                            {
                                label: 'Saques',
                                data: chartData.map(d => d.withdrawals),
                                borderColor: 'rgb(239, 68, 68)',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                tension: 0.3
                            },
                            {
                                label: 'NGR',
                                data: chartData.map(d => d.ngr),
                                borderColor: 'rgb(59, 130, 246)',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                tension: 0.3
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) {
                                        return 'R$ ' + value.toLocaleString('pt-BR');
                                    }
                                }
                            }
                        }
                    }
                });
            </script>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500">Afiliado não encontrado.</p>
            </div>
        @endif
    @else
        {{-- Lista de Afiliados --}}
        {{ $this->table }}
    @endif
</x-filament-panels::page>