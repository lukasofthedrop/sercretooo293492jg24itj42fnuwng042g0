<x-filament-panels::page>
    <style>
        /* TEMA LUCRATIVABET PROFISSIONAL - RELATÓRIOS */
        .affiliate-reports-container {
            background: transparent !important;
        }
        
        /* Cards principais com efeito glassmorphism */
        .lucra-card {
            background: rgba(30, 41, 59, 0.7) !important;
            border: 1px solid rgba(34, 197, 94, 0.3) !important;
            backdrop-filter: blur(10px) !important;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3), 
                        inset 0 1px 0 rgba(34, 197, 94, 0.1) !important;
            border-radius: 16px !important;
            padding: 24px !important;
            position: relative;
            overflow: hidden;
        }
        
        .lucra-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, transparent, #22c55e, transparent);
            animation: slideGlow 3s ease-in-out infinite;
        }
        
        @keyframes slideGlow {
            0%, 100% { transform: translateX(-100%); }
            50% { transform: translateX(100%); }
        }
        
        /* Cards de métricas com gradiente */
        .metric-card {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(16, 163, 74, 0.05) 100%) !important;
            border: 1px solid rgba(34, 197, 94, 0.2) !important;
            border-radius: 12px !important;
            padding: 20px !important;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(34, 197, 94, 0.2);
            border-color: #22c55e;
        }
        
        .metric-card::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(34, 197, 94, 0.1) 0%, transparent 70%);
            animation: pulse 4s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.3; }
        }
        
        /* Títulos e labels */
        .metric-label {
            color: rgba(241, 245, 249, 0.7) !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            margin-bottom: 8px !important;
        }
        
        .metric-value {
            color: #22c55e !important;
            font-size: 2.5rem !important;
            font-weight: 700 !important;
            text-shadow: 0 0 20px rgba(34, 197, 94, 0.5) !important;
            line-height: 1 !important;
            margin: 12px 0 !important;
        }
        
        .metric-subtitle {
            color: rgba(241, 245, 249, 0.6) !important;
            font-size: 0.875rem !important;
        }
        
        /* Seção de gráficos */
        .chart-container {
            background: rgba(15, 23, 42, 0.5) !important;
            border: 1px solid rgba(34, 197, 94, 0.2) !important;
            border-radius: 12px !important;
            padding: 24px !important;
            backdrop-filter: blur(5px) !important;
        }
        
        .chart-title {
            color: #f1f5f9 !important;
            font-size: 1.125rem !important;
            font-weight: 600 !important;
            margin-bottom: 20px !important;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        /* Tabela estilizada */
        .lucra-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .lucra-table thead {
            background: linear-gradient(90deg, rgba(34, 197, 94, 0.15) 0%, rgba(34, 197, 94, 0.05) 100%) !important;
        }
        
        .lucra-table th {
            color: #22c55e !important;
            font-weight: 600 !important;
            padding: 16px !important;
            text-align: left !important;
            border-bottom: 2px solid rgba(34, 197, 94, 0.3) !important;
            font-size: 0.875rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
        }
        
        .lucra-table tbody tr {
            transition: all 0.2s ease;
            border-bottom: 1px solid rgba(34, 197, 94, 0.1);
        }
        
        .lucra-table tbody tr:hover {
            background: rgba(34, 197, 94, 0.05) !important;
            transform: scale(1.01);
        }
        
        .lucra-table td {
            color: #f1f5f9 !important;
            padding: 14px 16px !important;
            border-bottom: 1px solid rgba(34, 197, 94, 0.05) !important;
        }
        
        /* Badges e tags */
        .tier-badge {
            padding: 6px 12px !important;
            border-radius: 20px !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            display: inline-block;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        
        .tier-bronze {
            background: linear-gradient(135deg, #CD7F32, #B87333) !important;
            color: white !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }
        
        .tier-silver {
            background: linear-gradient(135deg, #C0C0C0, #B8B8B8) !important;
            color: #333 !important;
        }
        
        .tier-gold {
            background: linear-gradient(135deg, #FFD700, #FFC700) !important;
            color: #333 !important;
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.4);
        }
        
        .tier-custom {
            background: linear-gradient(135deg, #8B008B, #9932CC) !important;
            color: white !important;
            box-shadow: 0 0 15px rgba(139, 0, 139, 0.4);
        }
        
        /* Status badges */
        .status-active {
            color: #22c55e !important;
            font-weight: 600;
        }
        
        .status-inactive {
            color: #ef4444 !important;
            font-weight: 600;
        }
        
        /* RevShare display */
        .revshare-display {
            background: rgba(34, 197, 94, 0.1);
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.875rem;
            margin: 2px 0;
        }
        
        .revshare-real {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444 !important;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            margin: 2px 0;
        }
        
        /* Código do afiliado */
        .affiliate-code {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e !important;
            padding: 4px 10px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-weight: 600;
            font-size: 0.875rem;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        
        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 40px;
            color: rgba(241, 245, 249, 0.5);
        }
        
        /* Animação de entrada */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* Glow verde para elementos importantes */
        .green-glow {
            filter: drop-shadow(0 0 10px rgba(34, 197, 94, 0.5));
        }
    </style>

    @php
        $metrics = $this->getMetrics();
        $topAffiliates = $this->getTopAffiliates();
        $monthlyData = $this->getMonthlyData();
        $tierDistribution = $this->getTierDistribution();
        $recentActivities = $this->getRecentActivities();
    @endphp

    <div class="affiliate-reports-container">
        {{-- Métricas Principais --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 fade-in">
            <div class="metric-card">
                <div class="metric-label">Total de Afiliados</div>
                <div class="metric-value">{{ number_format($metrics['total_affiliates']) }}</div>
                <div class="metric-subtitle">
                    <span class="text-green-400">{{ $metrics['active_affiliates'] }}</span> ativos
                </div>
            </div>
            
            <div class="metric-card">
                <div class="metric-label">Total de Indicados</div>
                <div class="metric-value">{{ number_format($metrics['total_referred']) }}</div>
                <div class="metric-subtitle">
                    Taxa de conversão: <span class="text-green-400">{{ $metrics['conversion_rate'] }}%</span>
                </div>
            </div>
            
            <div class="metric-card">
                <div class="metric-label">Comissões Pagas</div>
                <div class="metric-value">R$ {{ number_format($metrics['total_commissions'], 2, ',', '.') }}</div>
                <div class="metric-subtitle">Total acumulado no sistema</div>
            </div>
            
            <div class="metric-card">
                <div class="metric-label">NGR do Mês</div>
                <div class="metric-value">R$ {{ number_format($metrics['total_ngr'], 2, ',', '.') }}</div>
                <div class="metric-subtitle">
                    <span class="text-green-400">↑ R$ {{ number_format($metrics['total_deposits'], 0, ',', '.') }}</span> | 
                    <span class="text-red-400">↓ R$ {{ number_format($metrics['total_withdrawals'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Gráficos --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="chart-container fade-in" style="min-height: 350px;">
                <h3 class="chart-title">
                    <span class="green-glow">📊</span> NGR Últimos 6 Meses
                </h3>
                <div style="position: relative; height: 300px;">
                    <canvas id="ngrChart"></canvas>
                </div>
            </div>
            
            <div class="chart-container fade-in" style="min-height: 350px;">
                <h3 class="chart-title">
                    <span class="green-glow">🏆</span> Distribuição por Tier
                </h3>
                <div style="position: relative; height: 300px;">
                    <canvas id="tierChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Top 10 Afiliados --}}
        <div class="lucra-card mb-8 fade-in">
            <h3 class="chart-title mb-6">
                <span class="green-glow">🌟</span> Top 10 Afiliados - Performance
            </h3>
            
            @if(count($topAffiliates) > 0)
                <div class="overflow-x-auto">
                    <table class="lucra-table">
                        <thead>
                            <tr>
                                <th>Afiliado</th>
                                <th class="text-center">Código</th>
                                <th class="text-center">Indicados</th>
                                <th class="text-center">Tier</th>
                                <th class="text-center">NGR</th>
                                <th class="text-center">RevShare</th>
                                <th class="text-center">Saldo</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topAffiliates as $affiliate)
                            <tr>
                                <td>
                                    <div>
                                        <div style="color: #f1f5f9; font-weight: 600;">{{ $affiliate['name'] }}</div>
                                        <div style="color: rgba(241, 245, 249, 0.6); font-size: 0.875rem;">{{ $affiliate['email'] }}</div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="affiliate-code">{{ $affiliate['code'] }}</span>
                                </td>
                                <td class="text-center">
                                    <span style="color: #22c55e; font-weight: 700; font-size: 1.125rem;">
                                        {{ $affiliate['referred_count'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="tier-badge tier-{{ $affiliate['tier'] }}">
                                        @switch($affiliate['tier'])
                                            @case('bronze') 🥉 Bronze @break
                                            @case('silver') 🥈 Prata @break
                                            @case('gold') 🥇 Ouro @break
                                            @case('custom') 💎 Custom @break
                                        @endswitch
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div>
                                        <span class="{{ $affiliate['ngr_qualified'] ? 'text-green-400' : 'text-red-400' }}" 
                                              style="font-weight: 600;">
                                            R$ {{ number_format($affiliate['ngr'], 2, ',', '.') }}
                                        </span>
                                        <div style="font-size: 0.75rem; margin-top: 2px;">
                                            @if($affiliate['ngr_qualified'])
                                                <span class="text-green-400">✅ Qualificado</span>
                                            @else
                                                <span class="text-red-400">⚠️ Não qualificado</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 4px;">
                                        <div class="revshare-display">
                                            <span style="color: rgba(241, 245, 249, 0.6); font-size: 0.75rem;">RevShare:</span> 
                                            <span style="color: #22c55e; font-weight: 600;">{{ $affiliate['revshare_display'] }}%</span>
                                        </div>
                                        <div class="revshare-real">
                                            <span style="font-size: 0.75rem;">NGR:</span> 
                                            <span style="font-weight: 700;">{{ $affiliate['revshare_real'] }}%</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span style="color: #22c55e; font-weight: 700; font-size: 1.125rem;">
                                        R$ {{ number_format($affiliate['balance'], 2, ',', '.') }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($affiliate['is_active'])
                                        <span class="status-active">✅ Ativo</span>
                                    @else
                                        <span class="status-inactive">❌ Inativo</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <p>Nenhum afiliado encontrado.</p>
                </div>
            @endif
        </div>

        {{-- Atividades Recentes --}}
        <div class="lucra-card fade-in">
            <h3 class="chart-title mb-6">
                <span class="green-glow">📋</span> Atividades Recentes
            </h3>
            
            @if(count($recentActivities) > 0)
                <div class="overflow-x-auto">
                    <table class="lucra-table">
                        <thead>
                            <tr>
                                <th>Data/Hora</th>
                                <th>Afiliado</th>
                                <th>Indicado</th>
                                <th class="text-center">Tipo</th>
                                <th class="text-center">Comissão</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentActivities as $activity)
                            <tr>
                                <td style="color: rgba(241, 245, 249, 0.7); font-size: 0.875rem;">
                                    {{ $activity['date'] }}
                                </td>
                                <td style="color: #f1f5f9;">{{ $activity['inviter'] }}</td>
                                <td style="color: #f1f5f9;">{{ $activity['user'] }}</td>
                                <td class="text-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                          style="background: {{ $activity['type'] == 'revshare' ? 'rgba(59, 130, 246, 0.2)' : 'rgba(168, 85, 247, 0.2)' }}; 
                                                 color: {{ $activity['type'] == 'revshare' ? '#3b82f6' : '#a855f7' }};">
                                        {{ strtoupper($activity['type']) }}
                                    </span>
                                </td>
                                <td class="text-center" style="color: #22c55e; font-weight: 600;">
                                    @if($activity['type'] == 'revshare')
                                        {{ $activity['commission'] }}%
                                    @else
                                        R$ {{ number_format($activity['commission'], 2, ',', '.') }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold"
                                          style="background: {{ $activity['status'] == 'Pago' ? 'rgba(34, 197, 94, 0.2)' : 'rgba(251, 191, 36, 0.2)' }}; 
                                                 color: {{ $activity['status'] == 'Pago' ? '#22c55e' : '#fbbf24' }};">
                                        {{ $activity['status'] }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <p>Nenhuma atividade recente encontrada.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Scripts dos Gráficos --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configurações globais do Chart.js para tema dark
            Chart.defaults.color = '#f1f5f9';
            Chart.defaults.borderColor = 'rgba(34, 197, 94, 0.1)';
            
            // Gráfico NGR Mensal
            const ngrCtx = document.getElementById('ngrChart');
            if (ngrCtx) {
                const monthlyData = @json($monthlyData);
                
                new Chart(ngrCtx.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: monthlyData.map(d => d.month),
                        datasets: [
                            {
                                label: 'Depósitos',
                                data: monthlyData.map(d => d.deposits),
                                borderColor: '#22c55e',
                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                borderWidth: 2,
                                tension: 0.4,
                                pointBackgroundColor: '#22c55e',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            },
                            {
                                label: 'Saques',
                                data: monthlyData.map(d => d.withdrawals),
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                borderWidth: 2,
                                tension: 0.4,
                                pointBackgroundColor: '#ef4444',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6
                            },
                            {
                                label: 'NGR',
                                data: monthlyData.map(d => d.ngr),
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                borderWidth: 3,
                                tension: 0.4,
                                pointBackgroundColor: '#3b82f6',
                                pointBorderColor: '#fff',
                                pointBorderWidth: 2,
                                pointRadius: 5,
                                pointHoverRadius: 7
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 12,
                                        weight: '600'
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                borderColor: '#22c55e',
                                borderWidth: 1,
                                titleColor: '#22c55e',
                                bodyColor: '#f1f5f9',
                                padding: 12,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': R$ ' + 
                                               context.parsed.y.toLocaleString('pt-BR');
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    color: 'rgba(34, 197, 94, 0.05)',
                                    borderColor: 'rgba(34, 197, 94, 0.2)'
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    }
                                }
                            },
                            y: {
                                grid: {
                                    color: 'rgba(34, 197, 94, 0.05)',
                                    borderColor: 'rgba(34, 197, 94, 0.2)'
                                },
                                ticks: {
                                    font: {
                                        size: 11
                                    },
                                    callback: function(value) {
                                        return 'R$ ' + value.toLocaleString('pt-BR');
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Gráfico de Distribuição por Tier
            const tierCtx = document.getElementById('tierChart');
            if (tierCtx) {
                const tierData = @json($tierDistribution);
                
                new Chart(tierCtx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['🥉 Bronze', '🥈 Prata', '🥇 Ouro', '💎 Custom'],
                        datasets: [{
                            data: [
                                tierData.bronze || 0,
                                tierData.silver || 0,
                                tierData.gold || 0,
                                tierData.custom || 0
                            ],
                            backgroundColor: [
                                '#CD7F32',
                                '#C0C0C0',
                                '#FFD700',
                                '#8B008B'
                            ],
                            borderColor: '#0f172a',
                            borderWidth: 2,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 15,
                                    font: {
                                        size: 12,
                                        weight: '600'
                                    },
                                    generateLabels: function(chart) {
                                        const data = chart.data;
                                        return data.labels.map((label, i) => ({
                                            text: label + ' (' + data.datasets[0].data[i] + ')',
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            strokeStyle: data.datasets[0].backgroundColor[i],
                                            lineWidth: 0,
                                            hidden: false,
                                            index: i
                                        }));
                                    }
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                borderColor: '#22c55e',
                                borderWidth: 1,
                                titleColor: '#22c55e',
                                bodyColor: '#f1f5f9',
                                padding: 12,
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return label + ': ' + value + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-filament-panels::page>