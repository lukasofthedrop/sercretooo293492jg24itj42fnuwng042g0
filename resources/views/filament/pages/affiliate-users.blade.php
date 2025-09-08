<x-filament-panels::page>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <style>
        .stats-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.98) 0%, rgba(15, 23, 42, 0.98) 100%);
            border: 1px solid rgba(34, 197, 94, 0.25);
            border-radius: 12px;
            padding: 1.25rem;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(34, 197, 94, 0.2);
            border-color: rgba(34, 197, 94, 0.4);
        }
        
        .stat-label {
            color: rgba(241, 245, 249, 0.7);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .stat-value {
            color: #f1f5f9;
            font-size: 1.75rem;
            font-weight: 700;
        }
        
        .stat-value.highlight {
            color: #22c55e;
            text-shadow: 0 0 15px rgba(34, 197, 94, 0.4);
        }
        
        .stat-change {
            font-size: 0.75rem;
            color: rgba(241, 245, 249, 0.5);
            margin-top: 0.25rem;
        }
        
        .info-banner {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.1) 0%, rgba(34, 197, 94, 0.05) 100%);
            border: 1px solid rgba(34, 197, 94, 0.3);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 2rem;
        }
        
        .info-banner h3 {
            color: #22c55e;
            font-size: 1.125rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .info-banner p {
            color: rgba(241, 245, 249, 0.8);
            font-size: 0.875rem;
            line-height: 1.6;
        }
        
        .tips-section {
            background: rgba(30, 41, 59, 0.5);
            border: 1px solid rgba(241, 245, 249, 0.1);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 2rem;
        }
        
        .tips-section h4 {
            color: #f1f5f9;
            font-size: 1rem;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .tips-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .tips-list li {
            color: rgba(241, 245, 249, 0.7);
            font-size: 0.875rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid rgba(241, 245, 249, 0.05);
            display: flex;
            align-items: start;
            gap: 0.5rem;
        }
        
        .tips-list li:last-child {
            border-bottom: none;
        }
        
        .tips-list li i {
            color: #22c55e;
            margin-top: 0.125rem;
            flex-shrink: 0;
        }
    </style>
    
    <div class="affiliate-users-page">
        <!-- Banner Informativo -->
        <div class="info-banner">
            <h3><i class="fas fa-info-circle"></i> Sobre seus Indicados</h3>
            <p>
                Aqui você pode acompanhar detalhadamente todos os usuários que se cadastraram através do seu link de afiliado. 
                Monitore o desempenho, atividade e comissões geradas por cada indicado. 
                <strong style="color: #22c55e;">Você ganha {{ auth()->user()->affiliateSettings->revshare_display ?? 40 }}% de comissão</strong> sobre o NGR de cada usuário!
            </p>
        </div>
        
        <!-- Cards de Resumo -->
        <div class="stats-summary">
            @php
                $users = \App\Models\User::where('inviter', auth()->id())->get();
                $totalUsers = $users->count();
                $activeUsers = $users->filter(function($user) {
                    return \App\Models\Deposit::where('user_id', $user->id)
                        ->where('status', 1)
                        ->where('created_at', '>=', \Carbon\Carbon::now()->subDays(30))
                        ->exists();
                })->count();
                
                $totalDeposits = \App\Models\Deposit::whereIn('user_id', $users->pluck('id'))
                    ->where('status', 1)
                    ->sum('amount');
                    
                $totalWithdrawals = \App\Models\Withdrawal::whereIn('user_id', $users->pluck('id'))
                    ->where('status', 1)
                    ->sum('amount');
                    
                $totalNGR = $totalDeposits - $totalWithdrawals;
                $totalCommission = $totalNGR * 0.40; // Mostra 40%
                
                $conversionRate = $totalUsers > 0 ? ($activeUsers / $totalUsers) * 100 : 0;
            @endphp
            
            <div class="stat-card">
                <div class="stat-label">
                    <i class="fas fa-users"></i> Total de Indicados
                </div>
                <div class="stat-value">{{ number_format($totalUsers) }}</div>
                <div class="stat-change">Todos os tempos</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">
                    <i class="fas fa-user-check"></i> Usuários Ativos
                </div>
                <div class="stat-value highlight">{{ number_format($activeUsers) }}</div>
                <div class="stat-change">Últimos 30 dias</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">
                    <i class="fas fa-percentage"></i> Taxa de Conversão
                </div>
                <div class="stat-value">{{ number_format($conversionRate, 1) }}%</div>
                <div class="stat-change">Ativos / Total</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">
                    <i class="fas fa-chart-line"></i> NGR Total
                </div>
                <div class="stat-value">R$ {{ number_format($totalNGR, 2, ',', '.') }}</div>
                <div class="stat-change">Depósitos - Saques</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">
                    <i class="fas fa-coins"></i> Comissão Total
                </div>
                <div class="stat-value highlight">R$ {{ number_format($totalCommission, 2, ',', '.') }}</div>
                <div class="stat-change">40% do NGR</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-label">
                    <i class="fas fa-hand-holding-usd"></i> Ticket Médio
                </div>
                <div class="stat-value">
                    R$ {{ $totalUsers > 0 ? number_format($totalDeposits / $totalUsers, 2, ',', '.') : '0,00' }}
                </div>
                <div class="stat-change">Por usuário</div>
            </div>
        </div>
        
        <!-- Tabela de Usuários -->
        {{ $this->table }}
        
        <!-- Dicas para Melhorar Conversão -->
        <div class="tips-section">
            <h4><i class="fas fa-lightbulb"></i> Dicas para Aumentar suas Comissões</h4>
            <ul class="tips-list">
                <li>
                    <i class="fas fa-check"></i>
                    <span><strong>Engajamento:</strong> Mantenha contato regular com seus indicados, oferecendo suporte e dicas de jogos.</span>
                </li>
                <li>
                    <i class="fas fa-check"></i>
                    <span><strong>Conteúdo de Valor:</strong> Crie conteúdo educativo sobre estratégias de jogo responsável.</span>
                </li>
                <li>
                    <i class="fas fa-check"></i>
                    <span><strong>Promoções:</strong> Informe seus indicados sobre promoções e bônus especiais da plataforma.</span>
                </li>
                <li>
                    <i class="fas fa-check"></i>
                    <span><strong>Rede Social:</strong> Use suas redes sociais para compartilhar experiências positivas com a plataforma.</span>
                </li>
                <li>
                    <i class="fas fa-check"></i>
                    <span><strong>Transparência:</strong> Seja transparente sobre os benefícios de se cadastrar com seu link.</span>
                </li>
            </ul>
        </div>
    </div>
</x-filament-panels::page>