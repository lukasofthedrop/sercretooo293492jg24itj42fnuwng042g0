@extends('layouts.app')

@section('title', 'Dashboard do Afiliado - LucrativaBet')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/filament/admin/theme.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
/* Affiliate dashboard matching admin theme - green-black identity */
.dashboard-container {
    background-color: var(--gray-900, #111827);
    color: #F4F4F4;
    min-height: 100vh;
    padding: 2rem;
}

.header-affiliate {
    background: linear-gradient(to right, var(--gray-800), var(--gray-900));
    border-bottom: 2px solid var(--primary-500);
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 20px rgba(16, 185, 129, 0.1);
    margin-bottom: 2rem;
    border-radius: 12px;
}

.logo-affiliate {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-500, #10b981);
    text-transform: uppercase;
    letter-spacing: 1px;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.logo-affiliate i {
    animation: pulse-glow 2s infinite;
}

@keyframes pulse-glow {
    0%, 100% { text-shadow: 0 0 10px rgba(16, 185, 129, 0.8); }
    50% { text-shadow: 0 0 20px rgba(16, 185, 129, 1), 0 0 30px rgba(10, 184, 129, 0.6); }
}

.user-info-affiliate {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.container-affiliate {
    max-width: 1400px;
    margin: 0 auto;
}

.dashboard-title-affiliate {
    font-size: 2.5rem;
    margin-bottom: 2rem;
    background: linear-gradient(90deg, var(--primary-500), var(--primary-600));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.revshare-highlight-affiliate {
    background: linear-gradient(135deg, var(--gray-800, #1f2937) 0%, var(--gray-900, #111827) 100%);
    border: 2px solid var(--primary-500, #10b981);
    border-radius: 20px;
    padding: 2.5rem;
    margin-bottom: 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    box-shadow: 0 0 30px rgba(16, 185, 129, 0.2);
}

.revshare-highlight-affiliate::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, transparent 70%);
    animation: rotate 10s linear infinite;
}

@keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.revshare-value-affiliate {
    font-size: 5rem;
    font-weight: 700;
    color: var(--primary-500, #10b981);
    text-shadow: 0 0 40px rgba(16, 185, 129, 0.8), 0 0 60px rgba(10, 184, 129, 0.4);
    animation: pulse-scale 2s ease-in-out infinite;
    position: relative;
    z-index: 1;
}

@keyframes pulse-scale {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.08); }
}

.revshare-label-affiliate {
    font-size: 1.5rem;
    color: var(--gray-400, #9ca3af);
    margin-top: 0.5rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 3px;
    position: relative;
    z-index: 1;
}

.affiliate-code-section-affiliate {
    background: linear-gradient(135deg, var(--gray-800, #1f2937) 0%, var(--gray-900, #111827) 100%);
    border: 1px solid rgba(16, 185, 129, 0.3);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.code-display-affiliate {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: 1rem;
}

.code-text-affiliate {
    flex: 1;
    background: var(--gray-900, #111827);
    border: 2px solid rgba(16, 185, 129, 0.3);
    border-radius: 10px;
    padding: 1.25rem;
    font-family: 'Roboto Condensed', monospace;
    font-size: 1.5rem;
    color: var(--primary-500, #10b981);
    letter-spacing: 0.15em;
    font-weight: 600;
    text-align: center;
}

.copy-button-affiliate {
    background: linear-gradient(135deg, var(--primary-500, #10b981) 0%, var(--primary-600, #059669) 100%);
    color: #000000;
    border: none;
    border-radius: 10px;
    padding: 1.25rem 2.5rem;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.copy-button-affiliate:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(16, 185, 129, 0.5);
    background: linear-gradient(135deg, var(--primary-600), var(--primary-500));
}

.stats-grid-affiliate {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card-affiliate {
    background: linear-gradient(135deg, var(--gray-800, #1f2937) 0%, var(--gray-900, #111827) 100%);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(16, 185, 129, 0.2);
    border-radius: 16px;
    padding: 1.75rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card-affiliate::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, transparent, var(--primary-500), transparent);
    transform: translateX(-100%);
    animation: scan 3s linear infinite;
}

@keyframes scan {
    100% { transform: translateX(100%); }
}

.stat-card-affiliate:hover {
    transform: translateY(-6px);
    box-shadow: 0 15px 40px rgba(16, 185, 129, 0.15);
    border-color: var(--primary-500);
}

.stat-label-affiliate {
    color: var(--gray-400, #9ca3af);
    font-size: 0.9rem;
    margin-bottom: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    font-weight: 600;
}

.stat-value-affiliate {
    color: #F4F4F4;
    font-size: 2.25rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    font-family: 'Roboto Condensed', sans-serif;
}

.stat-value-affiliate.highlight {
    background: linear-gradient(90deg, var(--primary-500), var(--primary-600));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: none;
}

.stat-change-affiliate {
    font-size: 0.875rem;
    color: var(--gray-400, #9ca3af);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.stat-change-affiliate i {
    color: var(--primary-500);
}

.chart-container-affiliate {
    background: linear-gradient(135deg, var(--gray-800, #1f2937) 0%, var(--gray-900, #111827) 100%);
    border: 1px solid rgba(16, 185, 129, 0.2);
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.chart-title-affiliate {
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

.chart-title-affiliate i {
    color: var(--primary-500);
}

.table-container-affiliate {
    background: linear-gradient(135deg, var(--gray-800, #1f2937) 0%, var(--gray-900, #111827) 100%);
    border: 1px solid rgba(16, 185, 129, 0.2);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.table-header-affiliate {
    background: linear-gradient(90deg, var(--gray-800), var(--gray-900));
    padding: 1.25rem 1.5rem;
    border-bottom: 2px solid var(--primary-500);
}

.table-title-affiliate {
    color: #F4F4F4;
    font-size: 1.25rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
}

table.affiliate-table {
    width: 100%;
}

.affiliate-table th {
    background: var(--gray-800, #1f2937);
    color: var(--gray-400, #9ca3af);
    font-size: 0.9rem;
    font-weight: 600;
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid rgba(16, 185, 129, 0.2);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.affiliate-table td {
    color: #F4F4F4;
    padding: 1rem;
    border-bottom: 1px solid rgba(16, 185, 129, 0.1);
    font-weight: 500;
}

.affiliate-table tr:hover td {
    background: rgba(16, 185, 129, 0.05);
}

.status-badge-affiliate {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-badge-affiliate.active {
    background: rgba(16, 185, 129, 0.2);
    color: var(--primary-500);
    border: 1px solid rgba(16, 185, 129, 0.4);
}

.status-badge-affiliate.inactive {
    background: rgba(239, 68, 68, 0.2);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.3);
}

.commission-value-affiliate {
    color: var(--primary-500);
    font-weight: 700;
    text-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
}

/* No pink anywhere */
* [style*="pink"],
[class*="pink"] {
    background-color: var(--gray-900) !important;
    color: var(--primary-500) !important;
    border-color: var(--primary-500) !important;
}
</style>
@endsection

@section('content')
<div class="dashboard-container">
    <div class="header-affiliate">
        <div class="logo-affiliate">
            <i class="fas fa-coins"></i>
            <span>LucrativaBet <span style="font-weight: 300; color: var(--gray-500);">- AFILIADO</span></span>
        </div>
        <div class="user-info-affiliate">
            <span style="color: var(--gray-400); font-weight: 500;">{{ auth()->user()->email }}</span>
            <a href="{{ route('logout') }}" class="copy-button-affiliate" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>

    <div class="container-affiliate">
        <h1 class="dashboard-title-affiliate">
            <i class="fas fa-chart-line"></i> Dashboard do Afiliado
        </h1>

        <!-- RevShare em Destaque -->
        <div class="revshare-highlight-affiliate">
            <div class="revshare-value-affiliate">{{ $revshare_percentage }}%</div>
            <div class="revshare-label-affiliate">RevShare - Sua Comissão</div>
        </div>

        <!-- Código de Afiliado -->
        <div class="affiliate-code-section-affiliate">
            <h3 style="color: #F4F4F4; margin-bottom: 0.5rem;">
                <i class="fas fa-link"></i> Seu Código de Afiliado
            </h3>
            <div class="code-display-affiliate">
                <input type="text" class="code-text-affiliate" value="{{ $affiliate_code }}" readonly id="affiliateCode">
                <button class="copy-button-affiliate" onclick="copyCode()">
                    <i class="fas fa-copy"></i> Copiar Código
                </button>
            </div>
            <p style="color: var(--gray-400); margin-top: 0.5rem; font-size: 0.875rem;">
                Link de convite: <span style="color: var(--primary-500);">{{ $invite_link }}</span>
            </p>
        </div>

        <!-- Grid de Estatísticas -->
        <div class="stats-grid-affiliate">
            <div class="stat-card-affiliate">
                <div class="stat-label-affiliate">Total de Indicados</div>
                <div class="stat-value-affiliate">{{ number_format($total_referred) }}</div>
                <div class="stat-change-affiliate">
                    <i class="fas fa-users"></i> Total de usuários indicados
                </div>
            </div>
            
            <div class="stat-card-affiliate">
                <div class="stat-label-affiliate">Indicados Ativos</div>
                <div class="stat-value-affiliate highlight">{{ number_format($active_referred) }}</div>
                <div class="stat-change-affiliate">
                    <i class="fas fa-check-circle"></i> Ativos nos últimos 30 dias
                </div>
            </div>
            
            <div class="stat-card-affiliate">
                <div class="stat-label-affiliate">NGR do Mês</div>
                <div class="stat-value-affiliate">R$ {{ number_format($month_ngr, 2, ',', '.') }}</div>
                <div class="stat-change-affiliate">
                    <i class="fas fa-chart-line"></i> Receita líquida gerada
                </div>
            </div>
            
            <div class="stat-card-affiliate">
                <div class="stat-label-affiliate">Comissão Disponível</div>
                <div class="stat-value-affiliate highlight">R$ {{ number_format($available_balance, 2, ',', '.') }}</div>
                <div class="stat-change-affiliate">
                    <i class="fas fa-wallet"></i> Disponível para saque
                </div>
            </div>
        </div>

        <!-- Gráfico de Performance -->
        <div class="chart-container-affiliate">
            <h3 class="chart-title-affiliate">
                <i class="fas fa-chart-bar"></i>
                Performance Mensal - Comissão de {{ $revshare_percentage }}%
            </h3>
            <canvas id="performanceChart" height="80"></canvas>
        </div>

        <!-- Tabela de Indicados -->
        <div class="table-container-affiliate">
            <div class="table-header-affiliate">
                <h3 class="table-title-affiliate">
                    <i class="fas fa-users"></i> Seus Indicados Recentes
                </h3>
            </div>
            <table class="affiliate-table">
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
                            <span class="status-badge-affiliate {{ $referred['is_active'] ? 'active' : 'inactive' }}">
                                <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                {{ $referred['is_active'] ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td>R$ {{ number_format($referred['total_deposited'], 2, ',', '.') }}</td>
                        <td class="commission-value-affiliate">
                            R$ {{ number_format($referred['commission_generated'], 2, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--gray-400); padding: 2rem;">
                            <i class="fas fa-user-plus" style="font-size: 2rem; margin-bottom: 1rem; display: block; color: var(--primary-500);"></i>
                            Nenhum indicado ainda. Compartilhe seu código para começar a ganhar!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function copyCode() {
    const codeInput = document.getElementById('affiliateCode');
    codeInput.select();
    document.execCommand('copy');
    
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check"></i> Copiado!';
    button.style.background = 'linear-gradient(135deg, var(--success-600, #059669) 0%, var(--success-500, #10b981) 100%)';
    
    setTimeout(() => {
        button.innerHTML = originalText;
        button.style.background = '';
    }, 2000);
}

// Gráfico de Performance
document.addEventListener('DOMContentLoaded', function() {
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
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 2,
                    borderRadius: 6,
                    order: 2
                },
                {
                    label: 'Sua Comissão ({{ $revshare_percentage }}%)',
                    data: monthlyData.map(d => d.commission),
                    type: 'line',
                    borderColor: 'var(--primary-500, #10b981)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: 'var(--primary-500)',
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
                        color: 'rgba(244, 244, 244, 0.8)',
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(17, 24, 39, 0.95)',
                    borderColor: 'rgba(16, 185, 129, 0.3)',
                    borderWidth: 1,
                    titleColor: '#F4F4F4',
                    bodyColor: 'rgba(244, 244, 244, 0.8)',
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
                        color: 'rgba(244, 244, 244, 0.1)',
                        borderColor: 'rgba(244, 244, 244, 0.2)'
                    },
                    ticks: {
                        color: 'rgba(244, 244, 244, 0.6)',
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
                        color: 'rgba(244, 244, 244, 0.1)',
                        borderColor: 'rgba(244, 244, 244, 0.2)'
                    },
                    ticks: {
                        color: 'rgba(244, 244, 244, 0.6)'
                    }
                }
            }
        }
    });
});
</script>
@endsection
