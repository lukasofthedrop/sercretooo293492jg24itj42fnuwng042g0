<x-filament-panels::page>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <style>
        .statistics-container {
            display: grid;
            gap: 2rem;
        }
        
        .period-selector {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            background: rgba(30, 41, 59, 0.5);
            padding: 0.5rem;
            border-radius: 12px;
            border: 1px solid rgba(34, 197, 94, 0.2);
        }
        
        .period-button {
            background: transparent;
            border: 1px solid rgba(241, 245, 249, 0.2);
            color: rgba(241, 245, 249, 0.7);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.875rem;
        }
        
        .period-button.active {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            border-color: #22c55e;
            color: white;
        }
        
        .period-button:hover:not(.active) {
            background: rgba(34, 197, 94, 0.1);
            border-color: rgba(34, 197, 94, 0.3);
            color: #22c55e;
        }
        
        .stats-section {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.98) 100%);
            border: 1px solid rgba(34, 197, 94, 0.25);
            border-radius: 12px;
            padding: 1.5rem;
        }
        
        .section-title {
            color: #f1f5f9;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .section-title i {
            color: #22c55e;
        }
        
        .overview-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.25rem;
        }
        
        .overview-card {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(241, 245, 249, 0.1);
            border-radius: 8px;
            padding: 1.25rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .overview-card:hover {
            background: rgba(34, 197, 94, 0.05);
            border-color: rgba(34, 197, 94, 0.2);
            transform: translateY(-2px);
        }
        
        .overview-value {
            color: #22c55e;
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .overview-label {
            color: rgba(241, 245, 249, 0.7);
            font-size: 0.875rem;
        }
        
        .chart-container {
            background: rgba(15, 23, 42, 0.3);
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 1rem;
        }
        
        .chart-title {
            color: #f1f5f9;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .top-users-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        
        .top-users-table th {
            background: rgba(15, 23, 42, 0.5);
            color: rgba(241, 245, 249, 0.8);
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid rgba(34, 197, 94, 0.2);
        }
        
        .top-users-table td {
            color: #f1f5f9;
            padding: 0.75rem;
            border-bottom: 1px solid rgba(241, 245, 249, 0.05);
        }
        
        .top-users-table tr:hover td {
            background: rgba(34, 197, 94, 0.05);
        }
        
        .commission-value {
            color: #22c55e;
            font-weight: 600;
        }
        
        .weekday-bars {
            display: flex;
            align-items: end;
            gap: 0.5rem;
            height: 200px;
            padding: 1rem 0;
        }
        
        .weekday-bar {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }
        
        .weekday-bar-fill {
            background: linear-gradient(180deg, #22c55e 0%, #16a34a 100%);
            width: 100%;
            border-radius: 4px 4px 0 0;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .weekday-bar-fill:hover {
            background: linear-gradient(180deg, #16a34a 0%, #15803d 100%);
        }
        
        .weekday-label {
            color: rgba(241, 245, 249, 0.7);
            font-size: 0.75rem;
            text-align: center;
        }
        
        .retention-grid {
            display: grid;
            grid-template-columns: auto repeat(6, 1fr);
            gap: 1px;
            background: rgba(34, 197, 94, 0.1);
            border-radius: 8px;
            overflow: hidden;
            margin-top: 1rem;
        }
        
        .retention-cell {
            background: rgba(15, 23, 42, 0.8);
            padding: 0.75rem 0.5rem;
            text-align: center;
            font-size: 0.75rem;
            color: #f1f5f9;
        }
        
        .retention-header {
            background: rgba(34, 197, 94, 0.2);
            font-weight: 600;
        }
        
        .retention-percentage {
            font-weight: 600;
        }
        
        .retention-percentage.high {
            color: #22c55e;
        }
        
        .retention-percentage.medium {
            color: #fbbf24;
        }
        
        .retention-percentage.low {
            color: #ef4444;
        }
        
        .forecast-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .forecast-card {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-radius: 8px;
            padding: 1.25rem;
            text-align: center;
        }
        
        .forecast-month {
            color: rgba(241, 245, 249, 0.8);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }
        
        .forecast-value {
            color: #22c55e;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }
        
        .forecast-commission {
            color: rgba(241, 245, 249, 0.6);
            font-size: 0.75rem;
        }
        
        .growth-indicator {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        
        .growth-positive {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }
        
        .growth-negative {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
        }
        
        .hourly-heatmap {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 2px;
            margin-top: 1rem;
        }
        
        .hourly-cell {
            aspect-ratio: 1;
            border-radius: 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            color: #f1f5f9;
            transition: all 0.3s ease;
        }
        
        .hourly-cell:hover {
            transform: scale(1.1);
        }
    </style>
    
    <div class="statistics-container">
        <!-- Seletor de Período -->
        <div class="period-selector">
            <a href="{{ url()->current() }}?period=7" 
               class="period-button {{ request()->get('period', '30') == '7' ? 'active' : '' }}">
                Últimos 7 dias
            </a>
            <a href="{{ url()->current() }}?period=30" 
               class="period-button {{ request()->get('period', '30') == '30' ? 'active' : '' }}">
                Últimos 30 dias
            </a>
            <a href="{{ url()->current() }}?period=90" 
               class="period-button {{ request()->get('period', '30') == '90' ? 'active' : '' }}">
                Últimos 90 dias
            </a>
            <a href="{{ url()->current() }}?period=365" 
               class="period-button {{ request()->get('period', '30') == '365' ? 'active' : '' }}">
                Último ano
            </a>
        </div>
        
        <!-- Visão Geral -->
        <div class="stats-section">
            <h2 class="section-title">
                <i class="fas fa-chart-line"></i> Visão Geral - Últimos {{ request()->get('period', '30') }} dias
            </h2>
            
            <div class="overview-grid">
                <div class="overview-card">
                    <div class="overview-value">R$ {{ number_format($statistics['overview']['total_deposits'], 2, ',', '.') }}</div>
                    <div class="overview-label">Total Depositado</div>
                </div>
                
                <div class="overview-card">
                    <div class="overview-value">R$ {{ number_format($statistics['overview']['ngr'], 2, ',', '.') }}</div>
                    <div class="overview-label">NGR Gerado</div>
                </div>
                
                <div class="overview-card">
                    <div class="overview-value">R$ {{ number_format($statistics['overview']['commission'], 2, ',', '.') }}</div>
                    <div class="overview-label">Sua Comissão (40%)</div>
                </div>
                
                <div class="overview-card">
                    <div class="overview-value">{{ $statistics['overview']['active_users'] }}</div>
                    <div class="overview-label">Usuários Ativos</div>
                </div>
                
                <div class="overview-card">
                    <div class="overview-value">{{ number_format($statistics['overview']['conversion_rate'], 1) }}%</div>
                    <div class="overview-label">Taxa de Conversão</div>
                </div>
                
                <div class="overview-card">
                    <div class="overview-value">R$ {{ number_format($statistics['overview']['avg_user_value'], 2, ',', '.') }}</div>
                    <div class="overview-label">Valor Médio/Usuário</div>
                </div>
            </div>
        </div>
        
        <!-- Performance Mensal -->
        <div class="stats-section">
            <h2 class="section-title">
                <i class="fas fa-chart-bar"></i> Performance Mensal
            </h2>
            
            <div class="chart-container">
                <div class="chart-title">
                    <i class="fas fa-chart-area"></i>
                    Evolução do NGR e Comissões
                </div>
                <div style="position: relative; height: 300px;">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Top Usuários -->
        <div class="stats-section">
            <h2 class="section-title">
                <i class="fas fa-crown"></i> Top 10 Usuários por NGR
            </h2>
            
            <table class="top-users-table">
                <thead>
                    <tr>
                        <th>Posição</th>
                        <th>Usuário</th>
                        <th>Data Cadastro</th>
                        <th>Total Depositado</th>
                        <th>NGR</th>
                        <th>Sua Comissão</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($statistics['topUsers'] as $index => $user)
                    <tr>
                        <td>
                            @if($index == 0)
                                <i class="fas fa-trophy" style="color: #ffd700;"></i> {{ $index + 1 }}º
                            @elseif($index == 1)
                                <i class="fas fa-medal" style="color: #c0c0c0;"></i> {{ $index + 1 }}º
                            @elseif($index == 2)
                                <i class="fas fa-medal" style="color: #cd7f32;"></i> {{ $index + 1 }}º
                            @else
                                {{ $index + 1 }}º
                            @endif
                        </td>
                        <td>
                            <strong>{{ $user['name'] }}</strong><br>
                            <small style="color: rgba(241, 245, 249, 0.6);">{{ $user['email'] }}</small>
                        </td>
                        <td>{{ $user['created_at'] }}</td>
                        <td>R$ {{ number_format($user['total_deposits'], 2, ',', '.') }}</td>
                        <td>R$ {{ number_format($user['ngr'], 2, ',', '.') }}</td>
                        <td class="commission-value">R$ {{ number_format($user['commission'], 2, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: rgba(241, 245, 249, 0.5); padding: 2rem;">
                            Nenhum usuário encontrado no período selecionado
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Análise por Dia da Semana -->
        <div class="stats-section">
            <h2 class="section-title">
                <i class="fas fa-calendar-week"></i> Performance por Dia da Semana
            </h2>
            
            <div class="weekday-bars">
                @foreach($statistics['weekdayAnalysis'] as $day)
                @php
                    $maxValue = max(array_column($statistics['weekdayAnalysis'], 'total'));
                    $height = $maxValue > 0 ? ($day['total'] / $maxValue) * 150 : 0;
                @endphp
                <div class="weekday-bar">
                    <div class="weekday-bar-fill" 
                         style="height: {{ $height }}px;"
                         title="R$ {{ number_format($day['total'], 2, ',', '.') }}">
                    </div>
                    <div class="weekday-label">
                        <div>{{ $day['day'] }}</div>
                        <small>{{ $day['count'] }} transações</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Mapa de Calor por Hora -->
        <div class="stats-section">
            <h2 class="section-title">
                <i class="fas fa-clock"></i> Atividade por Horário
            </h2>
            
            <div class="hourly-heatmap">
                @foreach($statistics['hourlyAnalysis'] as $hour)
                @php
                    $maxHourly = max(array_column($statistics['hourlyAnalysis'], 'total'));
                    $intensity = $maxHourly > 0 ? $hour['total'] / $maxHourly : 0;
                    $opacity = max(0.1, $intensity);
                @endphp
                <div class="hourly-cell" 
                     style="background: rgba(34, 197, 94, {{ $opacity }});"
                     title="{{ $hour['hour'] }}: R$ {{ number_format($hour['total'], 2, ',', '.') }}">
                    <div>{{ substr($hour['hour'], 0, 2) }}</div>
                    <small>{{ $hour['count'] }}</small>
                </div>
                @endforeach
            </div>
            <p style="color: rgba(241, 245, 249, 0.6); font-size: 0.875rem; text-align: center; margin-top: 1rem;">
                Intensidade da cor indica maior volume de transações no horário
            </p>
        </div>
        
        <!-- Análise de Retenção -->
        <div class="stats-section">
            <h2 class="section-title">
                <i class="fas fa-users-cog"></i> Análise de Retenção por Coorte
            </h2>
            
            <div class="retention-grid">
                <div class="retention-cell retention-header">Coorte</div>
                <div class="retention-cell retention-header">Mês 0</div>
                <div class="retention-cell retention-header">Mês 1</div>
                <div class="retention-cell retention-header">Mês 2</div>
                <div class="retention-cell retention-header">Mês 3</div>
                <div class="retention-cell retention-header">Mês 4</div>
                <div class="retention-cell retention-header">Mês 5</div>
                
                @foreach($statistics['retention'] as $cohort)
                <div class="retention-cell">
                    <strong>{{ $cohort['cohort'] }}</strong><br>
                    <small>{{ $cohort['total'] }} usuários</small>
                </div>
                @foreach($cohort['retention'] as $retention)
                @php
                    $percentage = $retention['percentage'];
                    $class = $percentage >= 50 ? 'high' : ($percentage >= 25 ? 'medium' : 'low');
                @endphp
                <div class="retention-cell">
                    <div class="retention-percentage {{ $class }}">
                        {{ number_format($percentage, 1) }}%
                    </div>
                    <small>{{ $retention['count'] }}</small>
                </div>
                @endforeach
                @endforeach
            </div>
            <p style="color: rgba(241, 245, 249, 0.6); font-size: 0.875rem; margin-top: 1rem;">
                Mostra a porcentagem de usuários que permaneceram ativos em cada mês após o cadastro
            </p>
        </div>
        
        <!-- Projeção de Crescimento -->
        <div class="stats-section">
            <h2 class="section-title">
                <i class="fas fa-crystal-ball"></i> Projeção de Crescimento
            </h2>
            
            <div style="margin-bottom: 1rem;">
                <span class="growth-indicator {{ $statistics['forecast']['growth_rate'] >= 0 ? 'growth-positive' : 'growth-negative' }}">
                    <i class="fas fa-{{ $statistics['forecast']['growth_rate'] >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                    Taxa de Crescimento: {{ number_format(abs($statistics['forecast']['growth_rate']), 1) }}% ao mês
                </span>
            </div>
            
            <div class="forecast-cards">
                @foreach($statistics['forecast']['projections'] as $projection)
                <div class="forecast-card">
                    <div class="forecast-month">{{ $projection['month'] }}</div>
                    <div class="forecast-value">R$ {{ number_format($projection['projected_ngr'], 2, ',', '.') }}</div>
                    <div class="forecast-commission">
                        Comissão: R$ {{ number_format($projection['projected_commission'], 2, ',', '.') }}
                    </div>
                </div>
                @endforeach
            </div>
            
            <p style="color: rgba(241, 245, 249, 0.6); font-size: 0.875rem; margin-top: 1rem; text-align: center;">
                <i class="fas fa-info-circle"></i> 
                Projeções baseadas na performance dos últimos 3 meses. Resultados podem variar.
            </p>
        </div>
    </div>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('performanceChart').getContext('2d');
            const performanceData = @json($statistics['performance']);
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: performanceData.map(d => d.month),
                    datasets: [
                        {
                            label: 'NGR',
                            data: performanceData.map(d => d.ngr),
                            borderColor: '#22c55e',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.4,
                            fill: true,
                        },
                        {
                            label: 'Comissão (40%)',
                            data: performanceData.map(d => d.commission),
                            borderColor: '#3BC117',
                            backgroundColor: 'rgba(59, 193, 23, 0.1)',
                            tension: 0.4,
                            fill: true,
                        },
                        {
                            label: 'Novos Usuários',
                            data: performanceData.map(d => d.new_users),
                            type: 'bar',
                            backgroundColor: 'rgba(34, 197, 94, 0.3)',
                            borderColor: '#22c55e',
                            borderWidth: 1,
                            yAxisID: 'y1',
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
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(15, 23, 42, 0.95)',
                            borderColor: 'rgba(34, 197, 94, 0.3)',
                            borderWidth: 1,
                            titleColor: '#f1f5f9',
                            bodyColor: 'rgba(241, 245, 249, 0.8)',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(241, 245, 249, 0.1)',
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
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            beginAtZero: true,
                            grid: {
                                drawOnChartArea: false,
                            },
                            ticks: {
                                color: 'rgba(241, 245, 249, 0.6)',
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(241, 245, 249, 0.1)',
                            },
                            ticks: {
                                color: 'rgba(241, 245, 249, 0.6)'
                            }
                        }
                    }
                }
            });
        });
    </script>
</x-filament-panels::page>