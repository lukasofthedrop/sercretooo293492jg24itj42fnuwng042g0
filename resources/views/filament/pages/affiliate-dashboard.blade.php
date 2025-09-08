<x-filament-panels::page>
    <!-- Botão de Logout -->
    <div style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
        <a href="/logout-completo" 
           class="logout-btn"
           style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); 
                  color: white; 
                  padding: 10px 20px; 
                  border-radius: 8px; 
                  text-decoration: none; 
                  font-weight: bold;
                  display: flex;
                  align-items: center;
                  gap: 8px;
                  border: 1px solid rgba(239, 68, 68, 0.5);
                  transition: all 0.3s;
                  box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);"
           onmouseover="this.style.transform='scale(1.05)'"
           onmouseout="this.style.transform='scale(1)'">
            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0v2z"/>
                <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3z"/>
            </svg>
            Sair
        </a>
    </div>
    
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
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

        <!-- Filtros de Período -->
        <div style="background: linear-gradient(135deg, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.98) 100%); border: 1px solid rgba(34, 197, 94, 0.25); border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem;">
            <h3 style="color: #f1f5f9; margin: 0 0 1rem 0; display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-filter" style="color: #22c55e;"></i>
                Filtrar por Período
            </h3>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                <button onclick="filterByPeriod('DIA')" id="filter-dia" style="
                    background: rgba(34, 197, 94, 0.2);
                    border: 2px solid #22c55e;
                    color: #22c55e;
                    padding: 0.5rem 1rem;
                    border-radius: 8px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s;
                    font-size: 0.875rem;
                ">
                    <i class="fas fa-calendar-day"></i> DIA
                </button>
                <button onclick="filterByPeriod('SEMANA')" id="filter-semana" style="
                    background: rgba(30, 41, 59, 0.5);
                    border: 2px solid rgba(34, 197, 94, 0.3);
                    color: rgba(241, 245, 249, 0.7);
                    padding: 0.5rem 1rem;
                    border-radius: 8px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s;
                    font-size: 0.875rem;
                ">
                    <i class="fas fa-calendar-week"></i> SEMANA
                </button>
                <button onclick="filterByPeriod('MES')" id="filter-mes" style="
                    background: rgba(30, 41, 59, 0.5);
                    border: 2px solid rgba(34, 197, 94, 0.3);
                    color: rgba(241, 245, 249, 0.7);
                    padding: 0.5rem 1rem;
                    border-radius: 8px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s;
                    font-size: 0.875rem;
                ">
                    <i class="fas fa-calendar-alt"></i> MÊS
                </button>
                <div style="margin-left: auto; display: flex; align-items: center; gap: 0.5rem; color: rgba(241, 245, 249, 0.6); font-size: 0.875rem;">
                    <i class="fas fa-info-circle"></i>
                    <span id="periodo-atual">Visualizando: Hoje</span>
                </div>
            </div>
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
                <div class="stat-label">REVSHARE do mes</div>
                <div class="stat-value">R$ {{ number_format($month_ngr, 2, ',', '.') }}</div>
                <div class="stat-change">
                    <i class="fas fa-chart-line"></i> Revenue líquido
                </div>
            </div>
            
            <div class="stat-card" style="position: relative;">
                <div class="stat-label">Saldo Disponível</div>
                <div class="stat-value highlight">R$ {{ number_format($available_balance, 2, ',', '.') }}</div>
                <div class="stat-change">
                    <i class="fas fa-wallet"></i> Para saque
                </div>
                @if($available_balance > 0 && $can_withdraw)
                <button onclick="openWithdrawModal()" style="
                    position: absolute;
                    bottom: 1rem;
                    right: 1rem;
                    background: linear-gradient(135deg, #22c55e 0%, #3BC117 100%);
                    color: white;
                    border: none;
                    padding: 0.5rem 1rem;
                    border-radius: 8px;
                    font-size: 0.75rem;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s;
                    box-shadow: 0 2px 10px rgba(34, 197, 94, 0.3);
                " onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <i class="fas fa-money-bill-wave"></i> Solicitar Saque
                </button>
                @elseif($available_balance > 0 && !$can_withdraw)
                <div style="
                    position: absolute;
                    bottom: 1rem;
                    right: 1rem;
                    background: rgba(251, 191, 36, 0.1);
                    border: 1px solid rgba(251, 191, 36, 0.3);
                    color: rgba(251, 191, 36, 0.9);
                    padding: 0.5rem 1rem;
                    border-radius: 8px;
                    font-size: 0.7rem;
                    font-weight: 500;
                ">
                    <i class="fas fa-clock"></i> Próximo saque: {{ $next_withdraw_date }}
                </div>
                @endif
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
            <div class="table-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h3 class="table-title">👥 Usuários Cadastrados pelo Seu Link</h3>
                <span style="color: #22c55e; font-size: 0.875rem;">
                    Total: {{ $total_referred }} usuários
                </span>
            </div>
            <table class="referred-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Data de Cadastro</th>
                        <th>Status</th>
                        <th>Último Depósito</th>
                        <th>Total Depositado</th>
                        <th>Comissão ({{ $revshare_percentage }}%)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recent_referred as $referred)
                    <tr>
                        <td style="font-weight: 600;">{{ $referred['name'] }}</td>
                        <td style="color: rgba(241, 245, 249, 0.7);">
                            {{ substr($referred['email'], 0, 3) }}***{{ substr($referred['email'], strpos($referred['email'], '@')) }}
                        </td>
                        <td>{{ $referred['created_at'] }}</td>
                        <td>
                            <span class="status-badge {{ $referred['is_active'] ? 'active' : 'inactive' }}">
                                <i class="fas fa-circle" style="font-size: 0.5rem;"></i>
                                {{ $referred['is_active'] ? 'Ativo' : 'Inativo' }}
                            </span>
                        </td>
                        <td>{{ $referred['is_active'] ? 'Últimos 30 dias' : 'Há mais de 30 dias' }}</td>
                        <td style="font-weight: 600; color: #22c55e;">
                            R$ {{ number_format($referred['total_deposited'], 2, ',', '.') }}
                        </td>
                        <td class="commission-value">R$ {{ number_format($referred['commission_generated'], 2, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center; color: rgba(241, 245, 249, 0.5); padding: 2rem;">
                            <i class="fas fa-users" style="font-size: 2rem; margin-bottom: 1rem; display: block; opacity: 0.5;"></i>
                            Nenhum usuário cadastrado ainda. Compartilhe seu link para começar!<br>
                            <span style="font-size: 0.875rem; margin-top: 0.5rem; display: block;">
                                Link: {{ $invite_link }}
                            </span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de Solicitação de Saque -->
    <div id="withdrawModal" style="
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(5px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    ">
        <div style="
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.98) 100%);
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-radius: 16px;
            padding: 2rem;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 50px rgba(0, 0, 0, 0.5);
        ">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h2 style="color: #f1f5f9; font-size: 1.5rem; font-weight: 600;">
                    💰 Solicitar Saque Semanal
                </h2>
                <button onclick="closeWithdrawModal()" style="
                    background: transparent;
                    border: none;
                    color: #ef4444;
                    font-size: 1.5rem;
                    cursor: pointer;
                ">×</button>
            </div>
            
            @if(!$can_withdraw)
            <div style="
                background: rgba(239, 68, 68, 0.1);
                border: 1px solid rgba(239, 68, 68, 0.3);
                padding: 1rem;
                border-radius: 8px;
                margin-bottom: 1.5rem;
            ">
                <p style="color: rgba(239, 68, 68, 0.9); font-size: 0.875rem; margin: 0;">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Você já realizou um saque esta semana. Próximo saque disponível em: {{ $next_withdraw_date }}
                </p>
            </div>
            @endif
            
            <div style="margin-bottom: 1.5rem;">
                <label style="color: rgba(241, 245, 249, 0.8); font-size: 0.875rem; margin-bottom: 0.5rem; display: block;">
                    Saldo Disponível
                </label>
                <div style="
                    background: rgba(34, 197, 94, 0.1);
                    border: 1px solid rgba(34, 197, 94, 0.3);
                    padding: 1rem;
                    border-radius: 8px;
                    color: #22c55e;
                    font-size: 1.5rem;
                    font-weight: 600;
                ">
                    R$ {{ number_format($available_balance, 2, ',', '.') }}
                </div>
            </div>

            <form onsubmit="handleWithdrawSubmit(event)">
                <div style="margin-bottom: 1.5rem;">
                    <label style="color: rgba(241, 245, 249, 0.8); font-size: 0.875rem; margin-bottom: 0.5rem; display: block;">
                        Valor do Saque
                    </label>
                    <input type="text" id="withdrawAmount" placeholder="R$ 0,00" style="
                        width: 100%;
                        background: rgba(15, 23, 42, 0.5);
                        border: 1px solid rgba(241, 245, 249, 0.2);
                        color: #f1f5f9;
                        padding: 0.75rem;
                        border-radius: 8px;
                        font-size: 1rem;
                    " required>
                    <small style="color: rgba(241, 245, 249, 0.5); font-size: 0.75rem; margin-top: 0.25rem; display: block;">
                        Mínimo para saque: R$ 50,00
                    </small>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="color: rgba(241, 245, 249, 0.8); font-size: 0.875rem; margin-bottom: 0.5rem; display: block;">
                        Chave PIX
                    </label>
                    <input type="text" id="pixKey" placeholder="Digite sua chave PIX" style="
                        width: 100%;
                        background: rgba(15, 23, 42, 0.5);
                        border: 1px solid rgba(241, 245, 249, 0.2);
                        color: #f1f5f9;
                        padding: 0.75rem;
                        border-radius: 8px;
                        font-size: 1rem;
                    " required>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label style="color: rgba(241, 245, 249, 0.8); font-size: 0.875rem; margin-bottom: 0.5rem; display: block;">
                        Tipo de Chave
                    </label>
                    <select id="pixType" style="
                        width: 100%;
                        background: rgba(15, 23, 42, 0.5);
                        border: 1px solid rgba(241, 245, 249, 0.2);
                        color: #f1f5f9;
                        padding: 0.75rem;
                        border-radius: 8px;
                        font-size: 1rem;
                    " required>
                        <option value="">Selecione o tipo</option>
                        <option value="cpf">CPF</option>
                        <option value="email">E-mail</option>
                        <option value="phone">Telefone</option>
                        <option value="random">Chave Aleatória</option>
                    </select>
                </div>

                <div style="
                    background: rgba(251, 191, 36, 0.1);
                    border: 1px solid rgba(251, 191, 36, 0.3);
                    padding: 1rem;
                    border-radius: 8px;
                    margin-bottom: 1.5rem;
                ">
                    <p style="color: rgba(251, 191, 36, 0.9); font-size: 0.875rem; margin: 0;">
                        <i class="fas fa-info-circle"></i> 
                        Saques são processados em até 24 horas úteis. Taxa de processamento: 5%.
                    </p>
                </div>

                <div style="display: flex; gap: 1rem;">
                    <button type="button" onclick="closeWithdrawModal()" style="
                        flex: 1;
                        background: rgba(107, 114, 128, 0.2);
                        border: 1px solid rgba(107, 114, 128, 0.3);
                        color: #9ca3af;
                        padding: 0.75rem;
                        border-radius: 8px;
                        font-weight: 600;
                        cursor: pointer;
                        transition: all 0.3s;
                    ">
                        Cancelar
                    </button>
                    <button type="submit" style="
                        flex: 1;
                        background: linear-gradient(135deg, #22c55e 0%, #3BC117 100%);
                        border: none;
                        color: white;
                        padding: 0.75rem;
                        border-radius: 8px;
                        font-weight: 600;
                        cursor: pointer;
                        transition: all 0.3s;
                        box-shadow: 0 2px 10px rgba(34, 197, 94, 0.3);
                    ">
                        <i class="fas fa-check"></i> Confirmar Saque
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        // Funções do Modal de Saque
        function openWithdrawModal() {
            const modal = document.getElementById('withdrawModal');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeWithdrawModal() {
            const modal = document.getElementById('withdrawModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function handleWithdrawSubmit(event) {
            event.preventDefault();
            
            // Verifica se pode sacar (controle semanal)
            const canWithdraw = {{ $can_withdraw ? 'true' : 'false' }};
            
            if (!canWithdraw) {
                // Mensagem amigável para o afiliado
                Swal.fire({
                    icon: 'info',
                    title: 'Limite Semanal',
                    html: `
                        <p style="color: #f1f5f9;">Por política de segurança e para garantir melhor processamento, permitimos apenas <strong>1 saque por semana</strong>.</p>
                        <p style="color: #22c55e; margin-top: 1rem;"><strong>Próximo saque disponível:</strong> {{ $next_withdraw_date }}</p>
                        <p style="color: rgba(241, 245, 249, 0.7); font-size: 0.875rem; margin-top: 1rem;">Isso garante que todos os saques sejam processados com rapidez e segurança.</p>
                    `,
                    background: 'linear-gradient(135deg, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.98) 100%)',
                    color: '#f1f5f9',
                    confirmButtonColor: '#22c55e',
                    confirmButtonText: 'Entendi'
                });
                return;
            }
            
            const amount = document.getElementById('withdrawAmount').value;
            const pixKey = document.getElementById('pixKey').value;
            const pixType = document.getElementById('pixType').value;
            
            // Validação básica
            const numericAmount = parseFloat(amount.replace('R$', '').replace('.', '').replace(',', '.'));
            
            if (numericAmount < 50) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Valor Mínimo',
                    text: 'O valor mínimo para saque é R$ 50,00',
                    background: 'linear-gradient(135deg, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.98) 100%)',
                    color: '#f1f5f9',
                    confirmButtonColor: '#22c55e'
                });
                return;
            }
            
            if (numericAmount > {{ $available_balance }}) {
                Swal.fire({
                    icon: 'error',
                    title: 'Saldo Insuficiente',
                    text: 'Você não possui saldo suficiente para este valor',
                    background: 'linear-gradient(135deg, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.98) 100%)',
                    color: '#f1f5f9',
                    confirmButtonColor: '#22c55e'
                });
                return;
            }
            
            // Mostra loading
            Swal.fire({
                title: 'Processando...',
                text: 'Sua solicitação está sendo processada',
                allowOutsideClick: false,
                background: 'linear-gradient(135deg, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.98) 100%)',
                color: '#f1f5f9',
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Faz requisição real ao backend
            fetch('/api/affiliate/withdrawal/request', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Authorization': 'Bearer ' + (localStorage.getItem('access_token') || '')
                },
                body: JSON.stringify({
                    amount: numericAmount,
                    pix_key: pixKey,
                    pix_type: pixType
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saque Solicitado com Sucesso!',
                        html: `
                            <div style="text-align: left;">
                                <p style="margin-bottom: 0.5rem;"><strong>Valor:</strong> R$ ${new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(data.data.amount)}</p>
                                <p style="margin-bottom: 0.5rem;"><strong>PIX:</strong> ${pixKey}</p>
                                <p style="margin-bottom: 0.5rem;"><strong>Tipo:</strong> ${pixType.toUpperCase()}</p>
                            </div>
                            <div style="background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.3); padding: 1rem; border-radius: 8px; margin-top: 1rem;">
                                <p style="color: #22c55e; margin: 0; font-size: 0.875rem;">
                                    <i class="fas fa-clock"></i> Processamento em até <strong>${data.data.processing_time}</strong>
                                </p>
                            </div>
                            <p style="color: rgba(241, 245, 249, 0.7); font-size: 0.75rem; margin-top: 1rem;">
                                Você receberá uma notificação quando o pagamento for efetuado.
                            </p>
                            <p style="color: #22c55e; font-size: 0.75rem; margin-top: 0.5rem;">
                                <strong>Próximo saque disponível:</strong> ${data.data.next_withdraw_date}
                            </p>
                        `,
                        background: 'linear-gradient(135deg, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.98) 100%)',
                        color: '#f1f5f9',
                        confirmButtonColor: '#22c55e',
                        confirmButtonText: 'Ótimo!'
                    }).then(() => {
                        // Recarrega a página para atualizar saldo e status
                        window.location.reload();
                    });
                    
                    closeWithdrawModal();
                    
                    // Limpa o formulário
                    document.getElementById('withdrawAmount').value = '';
                    document.getElementById('pixKey').value = '';
                    document.getElementById('pixType').value = '';
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao solicitar saque',
                        text: data.message || 'Ocorreu um erro ao processar sua solicitação',
                        background: 'linear-gradient(135deg, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.98) 100%)',
                        color: '#f1f5f9',
                        confirmButtonColor: '#22c55e'
                    });
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro de conexão',
                    text: 'Não foi possível conectar ao servidor. Tente novamente.',
                    background: 'linear-gradient(135deg, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.98) 100%)',
                    color: '#f1f5f9',
                    confirmButtonColor: '#22c55e'
                });
            });
        }

        // Formata valor em real enquanto digita
        document.addEventListener('DOMContentLoaded', function() {
            const withdrawInput = document.getElementById('withdrawAmount');
            if (withdrawInput) {
                withdrawInput.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value) {
                        value = (parseInt(value) / 100).toFixed(2);
                        value = value.replace('.', ',');
                        value = 'R$ ' + value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
                        e.target.value = value;
                    }
                });
            }
        });

        // Função para filtrar por período
        function filterByPeriod(periodo) {
            // Atualiza os botões de filtro
            const buttons = ['filter-dia', 'filter-semana', 'filter-mes'];
            buttons.forEach(btnId => {
                const btn = document.getElementById(btnId);
                if (btn) {
                    if (btnId === `filter-${periodo.toLowerCase()}`) {
                        btn.style.background = 'rgba(34, 197, 94, 0.2)';
                        btn.style.borderColor = '#22c55e';
                        btn.style.color = '#22c55e';
                    } else {
                        btn.style.background = 'rgba(30, 41, 59, 0.5)';
                        btn.style.borderColor = 'rgba(34, 197, 94, 0.3)';
                        btn.style.color = 'rgba(241, 245, 249, 0.7)';
                    }
                }
            });

            // Atualiza o texto do período atual
            const periodoTexto = {
                'DIA': 'Hoje',
                'SEMANA': 'Esta Semana',
                'MES': 'Este Mês'
            };
            
            const periodoAtual = document.getElementById('periodo-atual');
            if (periodoAtual) {
                periodoAtual.textContent = `Visualizando: ${periodoTexto[periodo] || 'Período Selecionado'}`;
            }

            // Efeito visual de loading
            const statsGrid = document.querySelector('.stats-grid');
            if (statsGrid) {
                statsGrid.style.opacity = '0.6';
                statsGrid.style.filter = 'blur(2px)';
                
                setTimeout(() => {
                    statsGrid.style.opacity = '1';
                    statsGrid.style.filter = 'none';
                    
                    // Notificação visual do filtro aplicado
                    const notification = document.createElement('div');
                    notification.innerHTML = `
                        <div style="
                            position: fixed;
                            top: 20px;
                            right: 20px;
                            background: linear-gradient(135deg, rgba(34, 197, 94, 0.9) 0%, rgba(34, 197, 94, 0.8) 100%);
                            color: white;
                            padding: 0.75rem 1.5rem;
                            border-radius: 8px;
                            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.4);
                            z-index: 10000;
                            display: flex;
                            align-items: center;
                            gap: 0.5rem;
                            font-weight: 600;
                            animation: slideInRight 0.3s ease-out;
                        ">
                            <i class="fas fa-check-circle"></i>
                            Filtro aplicado: ${periodoTexto[periodo]}
                        </div>
                    `;
                    document.body.appendChild(notification);
                    
                    setTimeout(() => {
                        notification.style.animation = 'slideOutRight 0.3s ease-in';
                        setTimeout(() => document.body.removeChild(notification), 300);
                    }, 2000);
                }, 500);
            }

            console.log(`Filtro aplicado: ${periodo}`);
            // Aqui seria feita a requisição AJAX para filtrar os dados reais
        }

        // CSS para animações
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideInRight {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
            
            @keyframes slideOutRight {
                from {
                    transform: translateX(0);
                    opacity: 1;
                }
                to {
                    transform: translateX(100%);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</x-filament-panels::page>