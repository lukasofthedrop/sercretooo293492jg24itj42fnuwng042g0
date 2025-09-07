@extends('layouts.web')

@section('title', 'Dashboard de Afiliado - ' . auth()->user()->name)

@section('content')
<style>
    /* TEMA LUCRATIVABET - DASHBOARD AFILIADO */
    .affiliate-dashboard {
        min-height: 100vh;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        padding: 2rem 1rem;
    }
    
    /* Header com informações do afiliado */
    .affiliate-header {
        background: rgba(30, 41, 59, 0.7);
        border: 1px solid rgba(34, 197, 94, 0.3);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    
    .affiliate-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, transparent, #22c55e, transparent);
        animation: slideGlow 3s ease-in-out infinite;
    }
    
    @keyframes slideGlow {
        0%, 100% { transform: translateX(-100%); }
        50% { transform: translateX(100%); }
    }
    
    .welcome-text {
        color: #f1f5f9;
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .affiliate-code-box {
        background: rgba(34, 197, 94, 0.1);
        border: 2px solid #22c55e;
        border-radius: 12px;
        padding: 1rem;
        display: inline-block;
        margin-top: 1rem;
    }
    
    .affiliate-code {
        color: #22c55e;
        font-size: 1.5rem;
        font-weight: 700;
        font-family: 'Courier New', monospace;
        letter-spacing: 2px;
    }
    
    .copy-button {
        background: #22c55e;
        color: #0f172a;
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        margin-left: 1rem;
        transition: all 0.3s ease;
    }
    
    .copy-button:hover {
        background: #16a34a;
        transform: scale(1.05);
    }
    
    /* Cards de métricas */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }
    
    .metric-card {
        background: rgba(30, 41, 59, 0.7);
        border: 1px solid rgba(34, 197, 94, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px rgba(34, 197, 94, 0.2);
        border-color: #22c55e;
    }
    
    .metric-icon {
        width: 48px;
        height: 48px;
        background: rgba(34, 197, 94, 0.1);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .metric-label {
        color: rgba(241, 245, 249, 0.7);
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }
    
    .metric-value {
        color: #22c55e;
        font-size: 2rem;
        font-weight: 700;
        text-shadow: 0 0 20px rgba(34, 197, 94, 0.5);
    }
    
    .metric-subtitle {
        color: rgba(241, 245, 249, 0.6);
        font-size: 0.875rem;
        margin-top: 0.5rem;
    }
    
    /* Destaque especial para RevShare */
    .revshare-card {
        background: linear-gradient(135deg, rgba(34, 197, 94, 0.2) 0%, rgba(16, 163, 74, 0.1) 100%);
        border: 2px solid #22c55e;
        animation: pulseGlow 2s ease-in-out infinite;
    }
    
    @keyframes pulseGlow {
        0%, 100% { box-shadow: 0 0 20px rgba(34, 197, 94, 0.3); }
        50% { box-shadow: 0 0 40px rgba(34, 197, 94, 0.5); }
    }
    
    .revshare-value {
        font-size: 3rem !important;
        background: linear-gradient(90deg, #22c55e, #16a34a);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Tabela de indicados */
    .referred-section {
        background: rgba(30, 41, 59, 0.7);
        border: 1px solid rgba(34, 197, 94, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .section-title {
        color: #f1f5f9;
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .referred-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .referred-table thead {
        background: rgba(34, 197, 94, 0.1);
    }
    
    .referred-table th {
        color: #22c55e;
        font-weight: 600;
        padding: 1rem;
        text-align: left;
        border-bottom: 2px solid rgba(34, 197, 94, 0.3);
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .referred-table tbody tr {
        transition: all 0.2s ease;
    }
    
    .referred-table tbody tr:hover {
        background: rgba(34, 197, 94, 0.05);
    }
    
    .referred-table td {
        color: #f1f5f9;
        padding: 1rem;
        border-bottom: 1px solid rgba(34, 197, 94, 0.1);
    }
    
    .status-active {
        color: #22c55e;
        font-weight: 600;
    }
    
    .status-inactive {
        color: #ef4444;
        font-weight: 600;
    }
    
    /* Link de convite */
    .invite-section {
        background: rgba(30, 41, 59, 0.7);
        border: 1px solid rgba(34, 197, 94, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 2rem;
    }
    
    .invite-link-box {
        background: rgba(15, 23, 42, 0.9);
        border: 1px solid rgba(34, 197, 94, 0.3);
        border-radius: 8px;
        padding: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 1rem;
    }
    
    .invite-link {
        color: #22c55e;
        font-family: 'Courier New', monospace;
        word-break: break-all;
        flex: 1;
        margin-right: 1rem;
    }
    
    /* Gráfico de desempenho */
    .performance-chart {
        background: rgba(30, 41, 59, 0.7);
        border: 1px solid rgba(34, 197, 94, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        padding: 2rem;
        margin-bottom: 2rem;
        min-height: 400px;
    }
    
    /* Badge de aviso importante */
    .important-notice {
        background: linear-gradient(135deg, rgba(251, 191, 36, 0.2) 0%, rgba(245, 158, 11, 0.1) 100%);
        border: 1px solid #fbbf24;
        border-radius: 12px;
        padding: 1rem;
        margin: 2rem 0;
        color: #fbbf24;
        font-size: 0.875rem;
    }
    
    .important-notice strong {
        display: block;
        font-size: 1rem;
        margin-bottom: 0.5rem;
    }
</style>

<div class="affiliate-dashboard">
    <div class="container">
        <!-- Header com informações do afiliado -->
        <div class="affiliate-header">
            <h1 class="welcome-text">Bem-vindo, {{ auth()->user()->name }}! 🎯</h1>
            <p style="color: rgba(241, 245, 249, 0.8);">Acompanhe seu desempenho como afiliado da LucrativaBet</p>
            
            <div class="affiliate-code-box">
                <span style="color: rgba(241, 245, 249, 0.7); font-size: 0.875rem;">SEU CÓDIGO DE AFILIADO:</span><br>
                <span class="affiliate-code" id="affiliateCode">{{ auth()->user()->inviter_code ?? 'GERANDO...' }}</span>
                <button class="copy-button" onclick="copyCode()">📋 Copiar</button>
            </div>
        </div>

        <!-- Cards de Métricas -->
        <div class="metrics-grid">
            <!-- RevShare (FAKE - Mostra 40%) -->
            <div class="metric-card revshare-card">
                <div class="metric-icon">💰</div>
                <div class="metric-label">Sua Comissão RevShare</div>
                <div class="metric-value revshare-value">40%</div>
                <div class="metric-subtitle">Comissão sobre NGR dos indicados</div>
            </div>

            <!-- Total de Indicados -->
            <div class="metric-card">
                <div class="metric-icon">👥</div>
                <div class="metric-label">Total de Indicados</div>
                <div class="metric-value" id="totalReferred">0</div>
                <div class="metric-subtitle">Usuários cadastrados com seu código</div>
            </div>

            <!-- Indicados Ativos -->
            <div class="metric-card">
                <div class="metric-icon">✅</div>
                <div class="metric-label">Indicados Ativos</div>
                <div class="metric-value" id="activeReferred">0</div>
                <div class="metric-subtitle">Fizeram depósitos nos últimos 30 dias</div>
            </div>

            <!-- Saldo Disponível -->
            <div class="metric-card">
                <div class="metric-icon">💵</div>
                <div class="metric-label">Saldo Disponível</div>
                <div class="metric-value" id="availableBalance">R$ 0,00</div>
                <div class="metric-subtitle">Pronto para saque</div>
            </div>

            <!-- Total Ganho -->
            <div class="metric-card">
                <div class="metric-icon">📈</div>
                <div class="metric-label">Total Ganho</div>
                <div class="metric-value" id="totalEarned">R$ 0,00</div>
                <div class="metric-subtitle">Desde o início</div>
            </div>

            <!-- NGR do Mês -->
            <div class="metric-card">
                <div class="metric-icon">📊</div>
                <div class="metric-label">NGR do Mês</div>
                <div class="metric-value" id="monthNGR">R$ 0,00</div>
                <div class="metric-subtitle">Receita líquida gerada</div>
            </div>
        </div>

        <!-- Aviso Importante -->
        <div class="important-notice">
            <strong>⚠️ Importante:</strong>
            Suas comissões são calculadas com base no NGR (Net Gaming Revenue) dos seus indicados. 
            O NGR é a diferença entre depósitos e saques. Mantenha seus indicados ativos para maximizar seus ganhos!
        </div>

        <!-- Link de Convite -->
        <div class="invite-section">
            <h2 class="section-title">
                <span>🔗</span>
                Link de Convite
            </h2>
            <p style="color: rgba(241, 245, 249, 0.8); margin-bottom: 1rem;">
                Compartilhe este link para convidar novos usuários:
            </p>
            <div class="invite-link-box">
                <span class="invite-link" id="inviteLink">{{ url('/register?code=' . (auth()->user()->inviter_code ?? '')) }}</span>
                <button class="copy-button" onclick="copyLink()">📋 Copiar Link</button>
            </div>
        </div>

        <!-- Gráfico de Desempenho -->
        <div class="performance-chart">
            <h2 class="section-title">
                <span>📈</span>
                Desempenho Mensal
            </h2>
            <canvas id="performanceChart" height="100"></canvas>
        </div>

        <!-- Tabela de Indicados -->
        <div class="referred-section">
            <h2 class="section-title">
                <span>👥</span>
                Seus Indicados Recentes
            </h2>
            <div class="table-responsive">
                <table class="referred-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Data de Cadastro</th>
                            <th>Status</th>
                            <th>Total Depositado</th>
                            <th>Comissão Gerada</th>
                        </tr>
                    </thead>
                    <tbody id="referredTableBody">
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 2rem; color: rgba(241, 245, 249, 0.5);">
                                Carregando indicados...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Função para copiar código
    function copyCode() {
        const code = document.getElementById('affiliateCode').innerText;
        navigator.clipboard.writeText(code).then(() => {
            alert('Código copiado: ' + code);
        });
    }
    
    // Função para copiar link
    function copyLink() {
        const link = document.getElementById('inviteLink').innerText;
        navigator.clipboard.writeText(link).then(() => {
            alert('Link copiado com sucesso!');
        });
    }
    
    // Carregar métricas do afiliado
    async function loadMetrics() {
        try {
            const response = await fetch('/api/profile/affiliates/metrics', {
                headers: {
                    'Authorization': 'Bearer ' + localStorage.getItem('token'),
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                
                // Atualizar métricas (mostrando RevShare FAKE de 40%)
                document.getElementById('totalReferred').innerText = data.total_referred || 0;
                document.getElementById('activeReferred').innerText = data.active_referred || 0;
                document.getElementById('availableBalance').innerText = 'R$ ' + (data.available_balance || 0).toFixed(2).replace('.', ',');
                document.getElementById('totalEarned').innerText = 'R$ ' + (data.total_earned || 0).toFixed(2).replace('.', ',');
                document.getElementById('monthNGR').innerText = 'R$ ' + (data.month_ngr || 0).toFixed(2).replace('.', ',');
                
                // Atualizar tabela de indicados
                updateReferredTable(data.referred_users || []);
                
                // Renderizar gráfico
                renderPerformanceChart(data.monthly_data || []);
            }
        } catch (error) {
            console.error('Erro ao carregar métricas:', error);
        }
    }
    
    // Atualizar tabela de indicados
    function updateReferredTable(users) {
        const tbody = document.getElementById('referredTableBody');
        
        if (users.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" style="text-align: center; padding: 2rem; color: rgba(241, 245, 249, 0.5);">
                        Você ainda não tem indicados. Compartilhe seu link para começar!
                    </td>
                </tr>
            `;
            return;
        }
        
        tbody.innerHTML = users.map(user => `
            <tr>
                <td>${user.name}</td>
                <td>${new Date(user.created_at).toLocaleDateString('pt-BR')}</td>
                <td class="${user.is_active ? 'status-active' : 'status-inactive'}">
                    ${user.is_active ? '✅ Ativo' : '❌ Inativo'}
                </td>
                <td>R$ ${(user.total_deposited || 0).toFixed(2).replace('.', ',')}</td>
                <td style="color: #22c55e; font-weight: 600;">
                    R$ ${((user.total_deposited || 0) * 0.40).toFixed(2).replace('.', ',')}
                </td>
            </tr>
        `).join('');
    }
    
    // Renderizar gráfico de desempenho
    function renderPerformanceChart(monthlyData) {
        const ctx = document.getElementById('performanceChart');
        
        // Dados de exemplo se não houver dados reais
        if (!monthlyData || monthlyData.length === 0) {
            monthlyData = [
                {month: 'Jan', ngr: 1000, commission: 400},
                {month: 'Fev', ngr: 1500, commission: 600},
                {month: 'Mar', ngr: 2000, commission: 800},
                {month: 'Abr', ngr: 2500, commission: 1000},
                {month: 'Mai', ngr: 3000, commission: 1200},
                {month: 'Jun', ngr: 3500, commission: 1400}
            ];
        }
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthlyData.map(d => d.month),
                datasets: [
                    {
                        label: 'NGR Gerado',
                        data: monthlyData.map(d => d.ngr),
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Comissão (40%)',
                        data: monthlyData.map(d => d.commission),
                        borderColor: '#22c55e',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true
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
                            color: '#f1f5f9',
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
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': R$ ' + 
                                       context.parsed.y.toLocaleString('pt-BR');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(34, 197, 94, 0.05)'
                        },
                        ticks: {
                            color: '#f1f5f9',
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(34, 197, 94, 0.05)'
                        },
                        ticks: {
                            color: '#f1f5f9'
                        }
                    }
                }
            }
        });
    }
    
    // Carregar dados ao iniciar
    document.addEventListener('DOMContentLoaded', function() {
        loadMetrics();
        
        // Atualizar a cada 30 segundos
        setInterval(loadMetrics, 30000);
    });
</script>
@endsection