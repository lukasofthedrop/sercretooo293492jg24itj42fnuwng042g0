<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Afiliado - LucrativaBet</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto Condensed', sans-serif;
            background: #191A1E;
            color: #F4F4F4;
            min-height: 100vh;
            position: relative;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 50%, rgba(59, 193, 23, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 80% 80%, rgba(76, 227, 37, 0.08) 0%, transparent 50%);
            pointer-events: none;
        }
        
        .header {
            background: linear-gradient(to right, #24262B, #1C1E22);
            backdrop-filter: blur(10px);
            border-bottom: 2px solid #3BC117;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(59, 193, 23, 0.1);
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: #4ce325;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .logo i {
            animation: pulse-glow 2s infinite;
        }
        
        @keyframes pulse-glow {
            0%, 100% { text-shadow: 0 0 10px rgba(76, 227, 37, 0.8); }
            50% { text-shadow: 0 0 20px rgba(76, 227, 37, 1), 0 0 30px rgba(59, 193, 23, 0.6); }
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .dashboard-title {
            font-size: 2.5rem;
            margin-bottom: 2rem;
            background: linear-gradient(90deg, #4ce325, #3BC117);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .revshare-highlight {
            background: linear-gradient(135deg, #24262B 0%, #1C1E22 100%);
            border: 2px solid #3BC117;
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            box-shadow: 0 0 30px rgba(59, 193, 23, 0.2);
        }
        
        .revshare-highlight::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(76, 227, 37, 0.1) 0%, transparent 70%);
            animation: rotate 10s linear infinite;
        }
        
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .revshare-value {
            font-size: 5rem;
            font-weight: 700;
            color: #4ce325;
            text-shadow: 0 0 40px rgba(76, 227, 37, 0.8), 0 0 60px rgba(59, 193, 23, 0.4);
            animation: pulse-scale 2s ease-in-out infinite;
            position: relative;
            z-index: 1;
        }
        
        @keyframes pulse-scale {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.08); }
        }
        
        .revshare-label {
            font-size: 1.5rem;
            color: #98A7B5;
            margin-top: 0.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 3px;
            position: relative;
            z-index: 1;
        }
        
        .affiliate-code-section {
            background: linear-gradient(135deg, #24262B 0%, #1C1E22 100%);
            border: 1px solid rgba(59, 193, 23, 0.3);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        
        .code-display {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .code-text {
            flex: 1;
            background: #191A1E;
            border: 2px solid rgba(59, 193, 23, 0.3);
            border-radius: 10px;
            padding: 1.25rem;
            font-family: 'Roboto Condensed', monospace;
            font-size: 1.5rem;
            color: #4ce325;
            letter-spacing: 0.15em;
            font-weight: 600;
            text-align: center;
        }
        
        .copy-button {
            background: linear-gradient(135deg, #4ce325 0%, #3BC117 100%);
            color: #191A1E;
            border: none;
            border-radius: 10px;
            padding: 1.25rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(59, 193, 23, 0.3);
        }
        
        .copy-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(76, 227, 37, 0.5);
            background: linear-gradient(135deg, #5ff030 0%, #4ce325 100%);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #24262B 0%, #1C1E22 100%);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(59, 193, 23, 0.2);
            border-radius: 16px;
            padding: 1.75rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent, #4ce325, transparent);
            transform: translateX(-100%);
            animation: scan 3s linear infinite;
        }
        
        @keyframes scan {
            100% { transform: translateX(100%); }
        }
        
        .stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 40px rgba(59, 193, 23, 0.15);
            border-color: #3BC117;
        }
        
        .stat-label {
            color: #656E78;
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 600;
        }
        
        .stat-value {
            color: #F4F4F4;
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-family: 'Roboto Condensed', sans-serif;
        }
        
        .stat-value.highlight {
            background: linear-gradient(90deg, #4ce325, #3BC117);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: none;
        }
        
        .stat-change {
            font-size: 0.875rem;
            color: #98A7B5;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .stat-change i {
            color: #3BC117;
        }
        
        .chart-container {
            background: linear-gradient(135deg, #24262B 0%, #1C1E22 100%);
            border: 1px solid rgba(59, 193, 23, 0.2);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        
        .chart-title {
            color: #F4F4F4;
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .chart-title i {
            color: #3BC117;
        }
        
        .table-container {
            background: linear-gradient(135deg, #24262B 0%, #1C1E22 100%);
            border: 1px solid rgba(59, 193, 23, 0.2);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        
        .table-header {
            background: linear-gradient(90deg, #24262B, #1C1E22);
            padding: 1.25rem 1.5rem;
            border-bottom: 2px solid #3BC117;
        }
        
        .table-title {
            color: #F4F4F4;
            font-size: 1.25rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        table {
            width: 100%;
        }
        
        th {
            background: #191A1E;
            color: #656E78;
            font-size: 0.9rem;
            font-weight: 600;
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid rgba(59, 193, 23, 0.2);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        td {
            color: #F4F4F4;
            padding: 1rem;
            border-bottom: 1px solid rgba(59, 193, 23, 0.1);
            font-weight: 500;
        }
        
        tr:hover td {
            background: rgba(59, 193, 23, 0.05);
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
            background: rgba(59, 193, 23, 0.2);
            color: #4ce325;
            border: 1px solid rgba(59, 193, 23, 0.4);
        }
        
        .status-badge.inactive {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .commission-value {
            color: #4ce325;
            font-weight: 700;
            text-shadow: 0 0 10px rgba(76, 227, 37, 0.3);
        }
        
        .logout-btn {
            background: linear-gradient(135deg, #24262B, #191A1E);
            color: #98A7B5;
            border: 1px solid rgba(152, 167, 181, 0.3);
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .logout-btn:hover {
            background: linear-gradient(135deg, #3BC117, #4ce325);
            color: #191A1E;
            border-color: #4ce325;
            box-shadow: 0 4px 15px rgba(59, 193, 23, 0.3);
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <i class="fas fa-coins"></i> 
            <span>LucrativaBet <span style="font-weight: 300; color: #98A7B5;">| AFILIADO</span></span>
        </div>
        <div class="user-info">
            <span style="color: #98A7B5; font-weight: 500;">{{ $user->email }}</span>
            <a href="/admin" class="logout-btn">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>

    <div class="container">
        <h1 class="dashboard-title">
            <i class="fas fa-chart-line"></i> Dashboard do Afiliado
        </h1>

        <!-- RevShare em Destaque -->
        <div class="revshare-highlight">
            <div class="revshare-value">{{ $revshare_percentage }}%</div>
            <div class="revshare-label">RevShare - Sua Comissão</div>
        </div>

        <!-- Código de Afiliado -->
        <div class="affiliate-code-section">
            <h3 style="color: #f1f5f9; margin-bottom: 0.5rem;">
                <i class="fas fa-link"></i> Seu Código de Afiliado
            </h3>
            <div class="code-display">
                <input type="text" class="code-text" value="{{ $affiliate_code }}" readonly id="affiliateCode">
                <button class="copy-button" onclick="copyCode()">
                    <i class="fas fa-copy"></i> Copiar Código
                </button>
            </div>
            <p style="color: rgba(241, 245, 249, 0.6); margin-top: 0.5rem; font-size: 0.875rem;">
                Link de convite: <span style="color: #22c55e;">{{ $invite_link }}</span>
            </p>
        </div>

        <!-- Grid de Estatísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total de Indicados</div>
                <div class="stat-value">{{ number_format($total_referred) }}</div>
                <div class="stat-change">
                    <i class="fas fa-users"></i> Total de usuários indicados
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">Indicados Ativos</div>
                <div class="stat-value highlight">{{ number_format($active_referred) }}</div>
                <div class="stat-change">
                    <i class="fas fa-check-circle"></i> Ativos nos últimos 30 dias
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">NGR do Mês</div>
                <div class="stat-value">R$ {{ number_format($month_ngr, 2, ',', '.') }}</div>
                <div class="stat-change">
                    <i class="fas fa-chart-line"></i> Receita líquida gerada
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">Comissão Disponível</div>
                <div class="stat-value highlight">R$ {{ number_format($available_balance, 2, ',', '.') }}</div>
                <div class="stat-change">
                    <i class="fas fa-wallet"></i> Disponível para saque
                </div>
            </div>
        </div>

        <!-- Gráfico de Performance -->
        <div class="chart-container">
            <h3 class="chart-title">
                <i class="fas fa-chart-bar" style="color: #22c55e;"></i>
                Performance Mensal - Comissão de {{ $revshare_percentage }}%
            </h3>
            <canvas id="performanceChart" height="80"></canvas>
        </div>

        <!-- Tabela de Indicados -->
        <div class="table-container">
            <div class="table-header">
                <h3 class="table-title">
                    <i class="fas fa-users"></i> Seus Indicados Recentes
                </h3>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Data de Cadastro</th>
                        <th>Status</th>
                        <th>Total Depositado</th>
                        <th>Comissão Gerada ({{ $revshare_percentage }}%)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_referred as $referred)
                    <tr>
                        <td>{{ $referred['name'] }}</td>
                        <td>{{ $referred['email'] }}</td>
                        <td>{{ $referred['created_at'] }}</td>
                        <td>
                            <span class="status-badge {{ $referred['is_active'] ? 'active' : 'inactive' }}">
                                <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                {{ $referred['is_active'] ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td>R$ {{ number_format($referred['total_deposited'], 2, ',', '.') }}</td>
                        <td class="commission-value">
                            R$ {{ number_format($referred['commission_generated'], 2, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: rgba(241, 245, 249, 0.5); padding: 2rem;">
                            <i class="fas fa-user-plus" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                            Nenhum indicado ainda. Compartilhe seu código para começar a ganhar!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

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

        // Gráfico de Performance
        const ctx = document.getElementById('performanceChart').getContext('2d');
        const monthlyData = @json($monthly_data);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: monthlyData.map(d => d.month),
                datasets: [
                    {
                        label: 'NGR Gerado',
                        data: monthlyData.map(d => d.ngr),
                        backgroundColor: 'rgba(34, 197, 94, 0.2)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 2,
                        borderRadius: 6,
                        order: 2
                    },
                    {
                        label: 'Sua Comissão ({{ $revshare_percentage }}%)',
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
    </script>
</body>
</html>