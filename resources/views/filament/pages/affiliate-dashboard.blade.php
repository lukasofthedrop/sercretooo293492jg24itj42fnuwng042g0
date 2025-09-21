@php
    $statCards = [
        [
            'label' => 'Total de Indicados',
            'value' => number_format($total_referred),
            'icon'  => 'fa-users',
        ],
        [
            'label' => 'Indicados Ativos',
            'value' => number_format($active_referred),
            'icon'  => 'fa-user-check',
        ],
        [
            'label' => 'Saldo Disponível',
            'value' => 'R$ ' . number_format($available_balance, 2, ',', '.'),
            'icon'  => 'fa-wallet',
        ],
        [
            'label' => 'Revshare do Mês',
            'value' => 'R$ ' . number_format($month_ngr, 2, ',', '.'),
            'icon'  => 'fa-arrow-trend-up',
        ],
    ];
@endphp

<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Highlight: affiliate code & revshare --}}
        <div class="relative overflow-hidden rounded-2xl border border-[rgba(59,193,23,0.25)] bg-gradient-to-br from-[#0f1b33] to-[#081022] px-6 py-8 shadow-[0_25px_60px_rgba(4,23,9,0.55)]">
            <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.45em] text-[rgba(226,232,240,0.65)]">Programa de Afiliados</p>
                    <h2 class="mt-3 text-3xl font-semibold text-[#d9f99d] md:text-4xl">{{ $user->name }}</h2>
                    <p class="mt-2 max-w-xl text-sm text-[rgba(226,232,240,0.7)]">
                        Compartilhe seu link exclusivo, acompanhe as conversões em tempo real
                        e monitore sua performance sem sair do painel.
                    </p>
                </div>

                <div class="flex flex-col gap-4">
                    <div class="rounded-xl border border-[rgba(59,193,23,0.35)] bg-[rgba(59,193,23,0.08)] px-6 py-4 text-center">
                        <span class="text-xs uppercase tracking-[0.3em] text-[rgba(148,163,184,0.85)]">Revshare</span>
                        <p class="mt-2 text-4xl font-bold text-[#3bc117]">{{ $revshare_percentage }}%</p>
                    </div>
                    <div class="flex w-full items-center gap-3">
                        <input id="affiliate-code" class="flex-1 rounded-xl border border-[rgba(59,193,23,0.35)] bg-[#0a152b] px-4 py-3 text-center font-mono text-lg tracking-[0.25em] text-[#3bc117]" value="{{ $affiliate_code }}" readonly>
                        <button class="rounded-xl bg-gradient-to-br from-[#3bc117] to-[#2aa10f] px-5 py-3 text-sm font-semibold uppercase tracking-wider text-[#081407] shadow-[0_12px_30px_rgba(59,193,23,0.4)] transition hover:shadow-[0_16px_38px_rgba(59,193,23,0.4)]" onclick="copyAffiliateCode()">
                            Copiar
                        </button>
                    </div>
                    <p class="text-xs text-[rgba(226,232,240,0.6)]">
                        Link: <span class="font-mono text-[#3bc117]">{{ $invite_link }}</span>
                    </p>
                </div>
            </div>

            <div class="pointer-events-none absolute -right-32 -top-28 h-60 w-60 rounded-full bg-[rgba(59,193,23,0.08)] blur-3xl"></div>
        </div>

        {{-- Stats --}}
        <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($statCards as $stat)
                <div class="rounded-2xl border border-[rgba(59,193,23,0.18)] bg-[rgba(11,19,35,0.95)] p-5 shadow-[0_16px_40px_rgba(3,12,25,0.45)]">
                    <div class="flex items-center justify-between">
                        <p class="text-xs uppercase tracking-[0.35em] text-[rgba(148,163,184,0.7)]">{{ $stat['label'] }}</p>
                        <span class="text-[#3bc117]">
                            <i class="fa-solid {{ $stat['icon'] }} text-lg"></i>
                        </span>
                    </div>
                    <p class="mt-4 text-3xl font-semibold text-[#e2e8f0]">{{ $stat['value'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Performance chart --}}
            <div class="lg:col-span-2 rounded-2xl border border-[rgba(59,193,23,0.15)] bg-[rgba(8,14,29,0.96)] p-6 shadow-[0_18px_45px_rgba(3,12,25,0.45)]">
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-[#e2e8f0]">Performance dos últimos meses</h3>
                        <p class="text-xs uppercase tracking-[0.3em] text-[rgba(148,163,184,0.65)]">Depósitos x Saques x NGR</p>
                    </div>
                </div>
                <canvas id="performanceChart" height="160"></canvas>
            </div>

            {{-- Summary card --}}
            <div class="flex flex-col gap-4 rounded-2xl border border-[rgba(59,193,23,0.15)] bg-[rgba(8,14,29,0.96)] p-6 shadow-[0_18px_45px_rgba(3,12,25,0.45)]">
                <div>
                    <h4 class="text-lg font-semibold text-[#e2e8f0]">Resumo financeiro</h4>
                    <p class="mt-1 text-xs uppercase tracking-[0.25em] text-[rgba(148,163,184,0.65)]">Atualizado em tempo real</p>
                </div>
                <div class="space-y-3 text-sm text-[rgba(226,232,240,0.78)]">
                    <p class="flex items-center justify-between">
                        <span>Total Ganho</span>
                        <span class="font-semibold text-[#3bc117]">R$ {{ number_format($total_earned, 2, ',', '.') }}</span>
                    </p>
                    <p class="flex items-center justify-between">
                        <span>CPA disponível</span>
                        <span class="font-semibold">{{ $settings->cpa_value ? 'R$ ' . number_format($settings->cpa_value, 2, ',', '.') : '–' }}</span>
                    </p>
                    <p class="flex items-center justify-between">
                        <span>Próximo saque liberado</span>
                        <span>{{ $next_withdraw_date ?? 'Disponível' }}</span>
                    </p>
                    <p class="flex items-center justify-between">
                        <span>Revshare aplicado</span>
                        <span>{{ $revshare_percentage }}%</span>
                    </p>
                </div>
                <button
                    @if($available_balance <= 0 || ! $can_withdraw)
                        disabled
                    @endif
                    class="mt-4 rounded-xl bg-gradient-to-br from-[#3bc117] to-[#2aa10f] px-5 py-3 text-sm font-semibold uppercase tracking-wider text-[#081407] shadow-[0_12px_30px_rgba(59,193,23,0.4)] transition hover:shadow-[0_16px_38px_rgba(59,193,23,0.4)] disabled:cursor-not-allowed disabled:opacity-40">
                    Solicitar saque
                </button>
                <p class="text-xs leading-relaxed text-[rgba(148,163,184,0.65)]">
                    Saques liberados a cada 7 dias. Caso tenha dúvidas, entre em contato com o gerente de afiliados.
                </p>
            </div>
        </div>

        {{-- Recent referred table --}}
        <div class="rounded-2xl border border-[rgba(59,193,23,0.15)] bg-[rgba(8,14,29,0.96)] p-6 shadow-[0_22px_55px_rgba(3,12,25,0.45)]">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-[#e2e8f0]">Indicações recentes</h3>
                    <p class="text-xs uppercase tracking-[0.3em] text-[rgba(148,163,184,0.65)]">Atualizado automaticamente</p>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-[rgba(59,193,23,0.08)]">
                <table class="min-w-full divide-y divide-[rgba(148,163,184,0.12)]">
                    <thead class="bg-[rgba(9,17,33,0.92)]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.25em] text-[rgba(148,163,184,0.65)]">Afiliado</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.25em] text-[rgba(148,163,184,0.65)]">Cadastro</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.25em] text-[rgba(148,163,184,0.65)]">Depositado</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.25em] text-[rgba(148,163,184,0.65)]">Comissão</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.25em] text-[rgba(148,163,184,0.65)]">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[rgba(59,193,23,0.1)] bg-[rgba(8,14,29,0.9)] text-sm text-[rgba(226,232,240,0.85)]">
                        @forelse ($recent_referred as $ref)
                            <tr class="transition hover:bg-[rgba(59,193,23,0.08)]">
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-[#e2e8f0]">{{ $ref['name'] }}</div>
                                    <div class="text-xs text-[rgba(148,163,184,0.7)]">{{ $ref['email'] }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $ref['created_at'] }}</td>
                                <td class="px-4 py-3">R$ {{ number_format($ref['total_deposited'], 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-[#3bc117] font-semibold">R$ {{ number_format($ref['commission_generated'], 2, ',', '.') }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-2 rounded-full border border-[rgba(59,193,23,0.25)] px-3 py-1 text-xs font-semibold {{ $ref['is_active'] ? 'text-[#3bc117] bg-[rgba(59,193,23,0.12)]' : 'text-[#f87171] bg-[rgba(248,113,113,0.12)] border-[rgba(248,113,113,0.3)]' }}">
                                        <span class="h-2 w-2 rounded-full {{ $ref['is_active'] ? 'bg-[#3bc117]' : 'bg-[#f87171]' }}"></span>
                                        {{ $ref['is_active'] ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-[rgba(148,163,184,0.7)]">
                                    Nenhum indicado recente encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            const monthlyDataset = @json($monthly_data);
            const ctx = document.getElementById('performanceChart');

            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: monthlyDataset.map(item => item.month),
                        datasets: [
                            {
                                label: 'Depósitos',
                                data: monthlyDataset.map(item => item.deposits),
                                borderColor: '#3bc117',
                                backgroundColor: 'rgba(59,193,23,0.2)',
                                fill: true,
                                tension: 0.32,
                                borderWidth: 2,
                            },
                            {
                                label: 'Saques',
                                data: monthlyDataset.map(item => item.withdrawals),
                                borderColor: '#f87171',
                                backgroundColor: 'rgba(248,113,113,0.18)',
                                fill: true,
                                tension: 0.32,
                                borderWidth: 2,
                            },
                            {
                                label: 'NGR',
                                data: monthlyDataset.map(item => item.ngr),
                                borderColor: '#38bdf8',
                                backgroundColor: 'rgba(56,189,248,0.18)',
                                fill: false,
                                tension: 0.32,
                                borderWidth: 2,
                            }
                        ]
                    },
                    options: {
                        plugins: {
                            legend: {
                                labels: {
                                    color: 'rgba(226,232,240,0.78)',
                                    font: {
                                        family: 'Roboto Condensed'
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    color: 'rgba(148,163,184,0.75)',
                                },
                                grid: {
                                    color: 'rgba(59,193,23,0.08)'
                                }
                            },
                            y: {
                                ticks: {
                                    color: 'rgba(148,163,184,0.75)',
                                },
                                grid: {
                                    color: 'rgba(59,193,23,0.08)'
                                }
                            }
                        }
                    }
                });
            }

            function copyAffiliateCode() {
                const input = document.getElementById('affiliate-code');
                if (!input) return;

                navigator.clipboard.writeText(input.value).then(() => {
                    window.dispatchEvent(new CustomEvent('filament-notify', {
                        detail: {
                            status: 'success',
                            message: 'Código de afiliado copiado com sucesso!'
                        }
                    }));
                });
            }
        </script>
    @endpush
</x-filament-panels::page>
