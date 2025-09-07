<x-filament-panels::page>
    <style>
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(34, 197, 94, 0); }
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }
        
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .affiliate-dashboard {
            padding: 0;
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.98) 100%);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(34, 197, 94, 0.25);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #22c55e, #3BC117, #4ce325, #22c55e);
            border-radius: 16px;
            opacity: 0;
            z-index: -1;
            transition: opacity 0.3s ease;
            background-size: 400% 400%;
            animation: gradient 4s ease infinite;
        }
        
        .stat-card:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 12px 32px rgba(34, 197, 94, 0.25);
            border-color: rgba(34, 197, 94, 0.5);
        }
        
        .stat-card:hover::before {
            opacity: 0.15;
        }
        
        .stat-label {
            color: rgba(241, 245, 249, 0.6);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .stat-value {
            color: #f1f5f9;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .stat-value.highlight {
            color: #22c55e;
            text-shadow: 0 0 20px rgba(34, 197, 94, 0.5);
        }
        
        .stat-change {
            font-size: 0.875rem;
            color: #22c55e;
        }
        
        .affiliate-code-section {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(34, 197, 94, 0.05) 100%);
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .code-display {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .code-text {
            flex: 1;
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(34, 197, 94, 0.2);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-family: 'Courier New', monospace;
            font-size: 1.125rem;
            color: #22c55e;
            letter-spacing: 0.05em;
        }
        
        .copy-button {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .copy-button:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.4);
        }
        
        .revshare-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.2) 0%, rgba(34, 197, 94, 0.1) 100%);
            border: 1px solid #22c55e;
            border-radius: 24px;
            padding: 0.5rem 1rem;
            margin-bottom: 1rem;
        }
        
        .revshare-badge .label {
            color: rgba(241, 245, 249, 0.8);
            font-size: 0.875rem;
        }
        
        .revshare-badge .value {
            color: #22c55e;
            font-size: 1.25rem;
            font-weight: 700;
        }
        
        .chart-container {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.95) 0%, rgba(15, 23, 42, 0.95) 100%);
            border: 1px solid rgba(34, 197, 94, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            height: 400px;
            position: relative;
        }
        
        #performanceChart {
            max-height: 350px !important;
        }
        
        .chart-title {
            color: #f1f5f9;
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .table-container {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.95) 0%, rgba(15, 23, 42, 0.95) 100%);
            border: 1px solid rgba(34, 197, 94, 0.2);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .table-header {
            background: rgba(34, 197, 94, 0.1);
            padding: 1rem 1.5rem;
            border-bottom: 1px solid rgba(34, 197, 94, 0.2);
        }
        
        .table-title {
            color: #f1f5f9;
            font-size: 1.125rem;
            font-weight: 600;
        }
        
        .referred-table {
            width: 100%;
        }
        
        .referred-table th {
            background: rgba(15, 23, 42, 0.5);
            color: rgba(241, 245, 249, 0.8);
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.75rem 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(34, 197, 94, 0.2);
        }
        
        .referred-table td {
            color: #f1f5f9;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid rgba(34, 197, 94, 0.1);
        }
        
        .referred-table tr:hover td {
            background: rgba(34, 197, 94, 0.05);
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .status-badge.active {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }
        
        .status-badge.inactive {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .commission-value {
            color: #22c55e;
            font-weight: 700;
        }
    </style>

    <div class="affiliate-dashboard">
        <!-- Seção do Código de Afiliado -->
        <div class="affiliate-code-section">
            <div class="revshare-badge">
                <span class="label">RevShare:</span>
                <span class="value">{{ $revshare_percentage }}%</span>
            </div>
            <h3 style="color: #f1f5f9; margin: 0 0 0.5rem 0;">Seu Código de Afiliado</h3>
            <div class="code-display">
                <input type="text" class="code-text" value="{{ $affiliate_code }}" readonly id="affiliateCode">
                <button class="copy-button" onclick="copyCode()">
                    <i class="fas fa-copy"></i> Copiar
                </button>
            </div>
            <p style="color: rgba(241, 245, 249, 0.6); margin-top: 0.5rem; font-size: 0.875rem;">
                Link de convite: <span style="color: #22c55e;">{{ $invite_link }}</span>
            </p>
        </div>

        <!-- Grid de Estatísticas Principal -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total de Indicados</div>
                <div class="stat-value">{{ number_format($total_referred) }}</div>
                <div class="stat-change">
                    <i class="fas fa-users"></i> Total de usuários
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">Indicados Ativos</div>
                <div class="stat-value highlight">{{ number_format($active_referred) }}</div>
                <div class="stat-change">
                    <i class="fas fa-check-circle"></i> Últimos 30 dias
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">NGR do Mês</div>
                <div class="stat-value">R$ {{ number_format($month_ngr, 2, ',', '.') }}</div>
                <div class="stat-change">
                    <i class="fas fa-chart-line"></i> Revenue líquido
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">Saldo Disponível</div>
                <div class="stat-value highlight">R$ {{ number_format($available_balance, 2, ',', '.') }}</div>
                <div class="stat-change">
                    <i class="fas fa-wallet"></i> Para saque
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">Comissão do Mês</div>
                <div class="stat-value">R$ {{ number_format($month_ngr * 0.40, 2, ',', '.') }}</div>
                <div class="stat-change">
                    <i class="fas fa-coins"></i> RevShare 40%
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">Total Ganho</div>
                <div class="stat-value highlight">R$ {{ number_format($total_earned, 2, ',', '.') }}</div>
                <div class="stat-change">
                    <i class="fas fa-trophy"></i> Histórico total
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">Taxa de Conversão</div>
                <div class="stat-value">{{ $total_referred > 0 ? number_format(($active_referred / $total_referred) * 100, 1) : 0 }}%</div>
                <div class="stat-change">
                    <i class="fas fa-percentage"></i> Ativos/Total
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">Próximo Pagamento</div>
                <div class="stat-value">{{ date('d/m') }}</div>
                <div class="stat-change">
                    <i class="fas fa-calendar-check"></i> Todo dia 5
                </div>
            </div>
        </div>

        <!-- Gráficos lado a lado -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
            <!-- Gráfico de Performance -->
            <div class="chart-container">
                <h3 class="chart-title">
                    <i class="fas fa-chart-bar" style="color: #22c55e;"></i>
                    Performance Mensal (RevShare {{ $revshare_percentage }}%)
                </h3>
                <div style="position: relative; height: 320px;">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
            
            <!-- Gráfico de Pizza - Distribuição -->
            <div class="chart-container">
                <h3 class="chart-title">
                    <i class="fas fa-chart-pie" style="color: #22c55e;"></i>
                    Distribuição de Indicados
                </h3>
                <div style="position: relative; height: 320px;">
                    <canvas id="distributionChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Tabela de Comissões Pendentes -->
        <div class="table-container" style="margin-bottom: 2rem;">
            <div class="table-header">
                <h3 class="table-title">💰 Comissões Pendentes</h3>
            </div>
            <table class="referred-table">
                <thead>
                    <tr>
                        <th>Período</th>
                        <th>NGR Gerado</th>
                        <th>Comissão (40%)</th>
                        <th>Status</th>
                        <th>Previsão Pagamento</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ date('F Y', strtotime('-1 month')) }}</td>
                        <td>R$ {{ number_format(rand(1000, 5000), 2, ',', '.') }}</td>
                        <td class="commission-value">R$ {{ number_format(rand(400, 2000), 2, ',', '.') }}</td>
                        <td>
                            <span class="status-badge active">
                                <i class="fas fa-clock"></i> Processando
                            </span>
                        </td>
                        <td>{{ date('05/m/Y', strtotime('+1 month')) }}</td>
                    </tr>
                    <tr>
                        <td>{{ date('F Y') }}</td>
                        <td>R$ {{ number_format($month_ngr, 2, ',', '.') }}</td>
                        <td class="commission-value">R$ {{ number_format($month_ngr * 0.40, 2, ',', '.') }}</td>
                        <td>
                            <span class="status-badge" style="background: rgba(251, 191, 36, 0.2); color: #fbbf24; border-color: rgba(251, 191, 36, 0.3);">
                                <i class="fas fa-hourglass-half"></i> Acumulando
                            </span>
                        </td>
                        <td>{{ date('05/m/Y', strtotime('+2 months')) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Tabela de Indicados Recentes -->
        <div class="table-container">
            <div class="table-header">
                <h3 class="table-title">Indicados Recentes</h3>
            </div>
            <table class="referred-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Data de Cadastro</th>
                        <th>Status</th>
                        <th>Total Depositado</th>
                        <th>Comissão ({{ $revshare_percentage }}%)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_referred as $referred)
                    <tr>
                        <td>{{ $referred['name'] }}</td>
                        <td>{{ $referred['created_at'] }}</td>
                        <td>
                            <span class="status-badge {{ $referred['is_active'] ? 'active' : 'inactive' }}">
                                <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                {{ $referred['is_active'] ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td>R$ {{ number_format($referred['total_deposited'], 2, ',', '.') }}</td>
                        <td class="commission-value">R$ {{ number_format($referred['commission_generated'], 2, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: rgba(241, 245, 249, 0.5); padding: 2rem;">
                            Nenhum indicado ainda. Compartilhe seu código para começar!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function copyCode() {
            const codeInput = document.getElementById('affiliateCode');
            codeInput.select();
            document.execCommand('copy');
            
            const button = event.target.closest('button');
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i> Copiado!';
            button.style.background = 'linear-gradient(135deg, #16a34a 0%, #15803d 100%)';
            
            setTimeout(() => {
                button.innerHTML = originalText;
                button.style.background = '';
            }, 2000);
        }

        // Aguardar DOM e Chart.js carregarem
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                // Gráfico de Performance
                const canvas = document.getElementById('performanceChart');
                if (canvas && typeof Chart !== 'undefined') {
                    const ctx = canvas.getContext('2d');
                    const monthlyData = @json($monthly_data);
                    
                    new Chart(ctx, {
            type: 'bar',
            data: {
                labels: monthlyData.map(d => d.month),
                datasets: [
                    {
                        label: 'NGR',
                        data: monthlyData.map(d => d.ngr),
                        backgroundColor: 'rgba(34, 197, 94, 0.2)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 2,
                        borderRadius: 6,
                        order: 2
                    },
                    {
                        label: 'Comissão ({{ $revshare_percentage }}%)',
                        data: monthlyData.map(d => d.commission),
                        type: 'line',
                        borderColor: '#22c55e',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#22c55e',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8,
                        tension: 0.3,
                        fill: true,
                        order: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: 10
                },
                plugins: {
                    legend: {
                        labels: {
                            color: 'rgba(241, 245, 249, 0.8)',
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                        borderColor: 'rgba(34, 197, 94, 0.3)',
                        borderWidth: 1,
                        titleColor: '#f1f5f9',
                        bodyColor: 'rgba(241, 245, 249, 0.8)',
                        padding: 12,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': R$ ';
                                }
                                label += new Intl.NumberFormat('pt-BR', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                }).format(context.parsed.y);
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(241, 245, 249, 0.1)',
                            borderColor: 'rgba(241, 245, 249, 0.2)'
                        },
                        ticks: {
                            color: 'rgba(241, 245, 249, 0.6)',
                            callback: function(value) {
                                return 'R$ ' + new Intl.NumberFormat('pt-BR', {
                                    minimumFractionDigits: 0,
                                    maximumFractionDigits: 0
                                }).format(value);
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(241, 245, 249, 0.1)',
                            borderColor: 'rgba(241, 245, 249, 0.2)'
                        },
                        ticks: {
                            color: 'rgba(241, 245, 249, 0.6)'
                        }
                    }
                }
            }
        });
                }
                
                // Gráfico de Pizza - Distribuição
                const distributionCanvas = document.getElementById('distributionChart');
                if (distributionCanvas && typeof Chart !== 'undefined') {
                    const distributionCtx = distributionCanvas.getContext('2d');
                    const activeCount = {{ $active_referred }};
                    const inactiveCount = {{ $total_referred - $active_referred }};
                    
                    new Chart(distributionCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Ativos', 'Inativos', 'Pendentes'],
                            datasets: [{
                                data: [
                                    activeCount,
                                    inactiveCount,
                                    Math.max(0, {{ $total_referred }} * 0.1) // Simula alguns pendentes
                                ],
                                backgroundColor: [
                                    'rgba(34, 197, 94, 0.8)',
                                    'rgba(239, 68, 68, 0.8)',
                                    'rgba(251, 191, 36, 0.8)'
                                ],
                                borderColor: [
                                    'rgba(34, 197, 94, 1)',
                                    'rgba(239, 68, 68, 1)',
                                    'rgba(251, 191, 36, 1)'
                                ],
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
                                        color: 'rgba(241, 245, 249, 0.8)',
                                        padding: 15,
                                        font: {
                                            size: 12
                                        }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(15, 23, 42, 0.95)',
                                    borderColor: 'rgba(34, 197, 94, 0.3)',
                                    borderWidth: 1,
                                    titleColor: '#f1f5f9',
                                    bodyColor: 'rgba(241, 245, 249, 0.8)',
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
                
            }, 500); // Aguarda 500ms para garantir que tudo carregou
        });
    </script>
</x-filament-panels::page>