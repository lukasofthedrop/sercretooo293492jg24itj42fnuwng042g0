@extends('layouts.app')

@section('title', 'Dashboard do Afiliado - LucrativaBet')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/filament/admin/theme.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
/* FORCE ADMIN GREEN-BLACK IDENTITY - OVERRIDE ALL PINK/FROUNTEND COLORS */
:root {
    /* Primary color overrides - force green */
    --ci-primary-color: #10b981 !important;
    --ci-primary-opacity-color: rgba(16, 185, 129, 0.1) !important;
    --ci-secundary-color: #059669 !important;

    /* Home page colors override to black/green */
    --home_background: #111827 !important;
    --home_text_color: #f9fafb !important;
    --home_button_jogar: #10b981 !important;
    --home_hover_jogar: #059669 !important;
    --home_background_button_jogar: #10b981 !important;
    --home_background_button_banner: #10b981 !important;
    --home_icon_color_button_jogar: #000000 !important;
    --home_background_categorias: #374151 !important;
    --home_text_color_categorias: #f9fafb !important;
    --home_background_pesquisa: #374151 !important;
    --home_text_color_pesquisa: #9ca3af !important;
    --home_background_button_pesquisa: #10b981 !important;
    --home_icon_color_button_pesquisa: #000000 !important;
    --home_background_button_vertodos: #374151 !important;
    --home_text_color_button_vertodos: #f9fafb !important;
    --home_background_input_pesquisa: #1f2937 !important;
    --home_icon_color_input_pesquisa: #9ca3af !important;
    --home_border_color_input_pesquisa: #4b5563 !important;

    /* Navbar overrides to black/green */
    --navbar_background: #111827 !important;
    --navbar-text: #f9fafb !important;
    --navbar_button_background_login: #10b981 !important;
    --navbar_button_background_registro: #059669 !important;
    --navbar_button_border_color: #059669 !important;
    --navbar_button_text_login: #000000 !important;
    --navbar_button_text_registro: #000000 !important;
    --navbar_icon_menu: #f9fafb !important;
    --navbar_icon_promocoes: #10b981 !important;
    --navbar_icon_casino: #10b981 !important;
    --navbar_icon_sport: #10b981 !important;
    --navbar_button_text_superior: #f9fafb !important;
    --navbar_button_background_superior: #374151 !important;
    --navbar_button_deposito_background: #10b981 !important;
    --navbar_button_deposito_text_color: #000000 !important;
    --navbar_button_deposito_border_color: #059669 !important;
    --navbar_button_deposito_píx_color_text: #f9fafb !important;
    --navbar_button_deposito_píx_background: #059669 !important;
    --navbar_button_deposito_píx_icon: #000000 !important;
    --navbar_button_carteira_background: #374151 !important;
    --navbar_button_carteira_text_color: #f9fafb !important;
    --navbar_button_carteira_border_color: #4b5563 !important;
    --navbar_perfil_text_color: #f9fafb !important;
    --navbar_perfil_background: #374151 !important;
    --navbar_perfil_icon_color: #d1d5db !important;
    --navbar_perfil_icon_color_border: #4b5563 !important;
    --navbar_perfil_modal_icon_color: #d1d5db !important;
    --navbar_perfil_modal_text_modal: #f9fafb !important;
    --navbar_perfil_modal_background_modal: #1f2937 !important;
    --navbar_perfil_modal_hover_modal: #374151 !important;
    --navbar_icon_promocoes_segunda_cor: #10b981 !important;

    /* Sidebar overrides */
    --sidebar-background: #111827 !important;
    --sidebar-text-color: #f9fafb !important;
    --sidebar-button_missoes_background: #374151 !important;
    --sidebar-button_vip_background: #374151 !important;
    --sidebar-button_ganhe_background: #374151 !important;
    --sidebar-button_bonus_background: #374151 !important;
    --sidebar_button_missoes_text: #f9fafb !important;
    --sidebar_button_ganhe_text: #f9fafb !important;
    --sidebar_button_vip_text: #f9fafb !important;
    --sidebar-button_hover: #10b981 !important;
    --sidebar-text-hover: #000000 !important;
    --sidebar_border: #4b5563 !important;
    --sidebar_icons: #10b981 !important;
    --sidebar_icons_background: #374151 !important;

    /* Ensure no pink - override all possible pink elements */
    --ci-gray-dark: #1f2937 !important;
    --ci-gray-light: #9ca3af !important;
    --ci-gray-medium: #6b7280 !important;
    --ci-gray-over: #9ca3af !important;

    /* Force green for all primary elements */
    --primary-color: #10b981 !important;
    --danger-500: #ef4444 !important; /* Keep red for errors */
    --success-500: #10b981 !important;
    --warning-500: #f59e0b !important;
}

/* Ensure no pink classes or styles - extra overrides */
[class*="pink"],
[style*="pink"],
[style*="Pink"],
[style*="Rosa"],
.bg-pink-500,
.border-pink-500,
.text-pink-500,
.color-pink,
.rosa-50,
.rosa-100, 
.rosa-200, 
.rosa-300, 
.rosa-400, 
.rosa-500, 
.rosa-600, 
.rosa-700, 
.rosa-800, 
.rosa-900,
.bg-rosa-50,
.bg-rosa-100, 
.bg-rosa-200, 
.bg-rosa-300, 
.bg-rosa-400, 
.bg-rosa-500, 
.bg-rosa-600, 
.bg-rosa-700, 
.bg-rosa-800, 
.bg-rosa-900,
.text-rosa-50,
.text-rosa-100, 
.text-rosa-200, 
.text-rosa-300, 
.text-rosa-400, 
.text-rosa-500, 
.text-rosa-600, 
.text-rosa-700, 
.text-rosa-800, 
.text-rosa-900 {
    background-color: #1f2937 !important;
    color: #10b981 !important;
    border-color: #10b981 !important;
    text-shadow: none !important;
}

/* Body force dark mode with admin theme */
body.dark {
    background-color: #111827 !important;
    color: #f9fafb !important;
}

/* Affiliate dashboard matching admin theme - green-black identity */
.dashboard-container {
    background-color: #111827 !important;
    color: #f9fafb;
    min-height: 100vh;
    padding: 2rem;
    background: #111827 !important;
}

.header-affiliate {
    background: linear-gradient(to right, #1f2937, #111827) !important;
    border-bottom: 2px solid #10b981 !important;
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 20px rgba(16, 185, 129, 0.1) !important;
    margin-bottom: 2rem;
    border-radius: 12px;
    background: #1f2937 !important;
}

.logo-affiliate {
    font-size: 1.5rem;
    font-weight: 700;
    color: #10b981 !important;
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
    background: linear-gradient(90deg, #10b981, #059669) !important;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 2px;
    color: #10b981 !important;
}

.revshare-highlight-affiliate {
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%) !important;
    border: 2px solid #10b981 !important;
    border-radius: 20px;
    padding: 2.5rem;
    margin-bottom: 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
    box-shadow: 0 0 30px rgba(16, 185, 129, 0.2) !important;
}

.revshare-highlight-affiliate::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(16, 185, 129, 0.1) 0%, transparent 70%) !important;
    animation: rotate 10s linear infinite;
}

@keyframes rotate {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.revshare-value-affiliate {
    font-size: 5rem;
    font-weight: 700;
    color: #10b981 !important;
    text-shadow: 0 0 40px rgba(16, 185, 129, 0.8), 0 0 60px rgba(10, 184, 129, 0.4) !important;
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
    color: #9ca3af !important;
    margin-top: 0.5rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 3px;
    position: relative;
    z-index: 1;
}

.affiliate-code-section-affiliate {
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%) !important;
    border: 1px solid rgba(16, 185, 129, 0.3) !important;
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3) !important;
}

.code-display-affiliate {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: 1rem;
}

.code-text-affiliate {
    flex: 1;
    background: #111827 !important;
    border: 2px solid rgba(16, 185, 129, 0.3) !important;
    border-radius: 10px;
    padding: 1.25rem;
    font-family: 'Roboto Condensed', monospace;
    font-size: 1.5rem;
    color: #10b981 !important;
    letter-spacing: 0.15em;
    font-weight: 600;
    text-align: center;
}

.copy-button-affiliate {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    color: #000000 !important;
    border: none;
    border-radius: 10px;
    padding: 1.25rem 2.5rem;
    font-size: 1.1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease !important;
    text-transform: uppercase;
    letter-spacing: 1px;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3) !important;
}

.copy-button-affiliate:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 30px rgba(16, 185, 129, 0.5) !important;
    background: linear-gradient(135deg, #059669, #10b981) !important;
}

.stats-grid-affiliate {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card-affiliate {
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%) !important;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(16, 185, 129, 0.2) !important;
    border-radius: 16px;
    padding: 1.75rem;
    transition: all 0.3s ease !important;
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
    background: linear-gradient(90deg, transparent, #10b981, transparent) !important;
    transform: translateX(-100%);
    animation: scan 3s linear infinite;
}

@keyframes scan {
    100% { transform: translateX(100%); }
}

.stat-card-affiliate:hover {
    transform: translateY(-6px) !important;
    box-shadow: 0 15px 40px rgba(16, 185, 129, 0.15) !important;
    border-color: #10b981 !important;
}

.stat-label-affiliate {
    color: #9ca3af !important;
    font-size: 0.9rem;
    margin-bottom: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    font-weight: 600;
}

.stat-value-affiliate {
    color: #f9fafb !important;
    font-size: 2.25rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    font-family: 'Roboto Condensed', sans-serif;
}

.stat-value-affiliate.highlight {
    background: linear-gradient(90deg, #10b981, #059669) !important;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    text-shadow: none !important;
}

.stat-change-affiliate {
    font-size: 0.875rem;
    color: #9ca3af !important;
    display: flex !important;
    align-items: center;
    gap: 0.5rem;
}

.stat-change-affiliate i {
    color: #10b981 !important;
}

.chart-container-affiliate {
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%) !important;
    border: 1px solid rgba(16, 185, 129, 0.2) !important;
    border-radius: 16px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3) !important;
}

.chart-title-affiliate {
    color: #f9fafb !important;
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
    color: #10b981 !important;
}

.table-container-affiliate {
    background: linear-gradient(135deg, #1f2937 0%, #111827 100%) !important;
    border: 1px solid rgba(16, 185, 129, 0.2) !important;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3) !important;
}

.table-header-affiliate {
    background: linear-gradient(90deg, #1f2937, #111827) !important;
    padding: 1.25rem 1.5rem;
    border-bottom: 2px solid #10b981 !important;
}

.table-title-affiliate {
    color: #f9fafb !important;
    font-size: 1.25rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
}

table.affiliate-table {
    width: 100%;
}

.affiliate-table th {
    background: #111827 !important;
    color: #9ca3af !important;
    font-size: 0.9rem;
    font-weight: 600;
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid rgba(16, 185, 129, 0.2) !important;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.affiliate-table td {
    color: #f9fafb !important;
    padding: 1rem;
    border-bottom: 1px solid rgba(16, 185, 129, 0.1) !important;
    font-weight: 500;
}

.affiliate-table tr:hover td {
    background: rgba(16, 185, 129, 0.05) !important;
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
    background: rgba(16, 185, 129, 0.2) !important;
    color: #10b981 !important;
    border: 1px solid rgba(16, 185, 129, 0.4) !important;
}

.status-badge-affiliate.inactive {
    background: rgba(239, 68, 68, 0.2) !important;
    color: #ef4444 !important;
    border: 1px solid rgba(239, 68, 68, 0.3) !important;
}

.commission-value-affiliate {
    color: #10b981 !important;
    font-weight: 700;
    text-shadow: 0 0 10px rgba(16, 185, 129, 0.3) !important;
}

/* Scrollbar for dark theme */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #111827;
}

::-webkit-scrollbar-thumb {
    background: #059669;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #10b981;
}
</style>
@endsection

@section('content')
<div class="dashboard-container">
    <div class="header-affiliate">
        <div class="logo-affiliate">
            <i class="fas fa-coins"></i>
            <span>LucrativaBet <span style="font-weight: 300; color: #6b7280;">- AFILIADO</span></span>
        </div>
        <div class="user-info-affiliate">
            <span style="color: #9ca3af; font-weight: 500;">{{ auth()->user()->email }}</span>
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
            <h3 style="color: #f9fafb; margin-bottom: 0.5rem;">
                <i class="fas fa-link"></i> Seu Código de Afiliado
            </h3>
            <div class="code-display-affiliate">
                <input type="text" class="code-text-affiliate" value="{{ $affiliate_code }}" readonly id="affiliateCode">
                <button class="copy-button-affiliate" onclick="copyCode()">
                    <i class="fas fa-copy"></i> Copiar Código
                </button>
            </div>
            <p style="color: #9ca3af; margin-top: 0.5rem; font-size: 0.875rem;">
                Link de convite: <span style="color: #10b981;">{{ $invite_link }}</span>
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
                <i class="fas fa-chart-bar" style="color: #10b981;"></i>
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
                        <td colspan="6" style="text-align: center; color: #9ca3af; padding: 2rem;">
                            <i class="fas fa-user-plus" style="font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
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
    button.style.background = 'linear-gradient(135deg, #059669 0%, #10b981 100%) !important';
    
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
                    backgroundColor: 'rgba(16, 185, 129, 0.2) !important',
                    borderColor: 'rgba(16, 185, 129, 1) !important',
                    borderWidth: 2,
                    borderRadius: 6,
                    order: 2
                },
                {
                    label: 'Sua Comissão ({{ $revshare_percentage }}%)',
                    data: monthlyData.map(d => d.commission),
                    type: 'line',
                    borderColor: '#10b981 !important',
                    backgroundColor: 'rgba(16, 185, 129, 0.1) !important',
                    borderWidth: 3,
                    pointBackgroundColor: '#10b981 !important',
                    pointBorderColor: '#fff !important',
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
                        color: 'rgba(249, 250, 251, 0.8) !important',
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(17, 24, 39, 0.95) !important',
                    borderColor: 'rgba(16, 185, 129, 0.3) !important',
                    borderWidth: 1,
                    titleColor: '#f9fafb !important',
                    bodyColor: 'rgba(249, 250, 251, 0.8) !important',
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
                        color: 'rgba(249, 250, 251, 0.1) !important',
                        borderColor: 'rgba(249, 250, 251, 0.2) !important'
                    },
                    ticks: {
                        color: 'rgba(249, 250, 251, 0.6) !important',
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
                        color: 'rgba(249, 250, 251, 0.1) !important',
                        borderColor: 'rgba(249, 250, 251, 0.2) !important'
                    },
                    ticks: {
                        color: 'rgba(249, 250, 251, 0.6) !important'
                    }
                }
            }
        }
    });
});
</script>
@endsection
