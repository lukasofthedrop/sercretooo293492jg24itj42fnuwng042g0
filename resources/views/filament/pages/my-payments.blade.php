<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Cards de Resumo Financeiro -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Saldo Disponível -->
            <div class="bg-gradient-to-br from-green-600 to-green-700 rounded-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">Saldo Disponível</p>
                        <p class="text-3xl font-bold mt-2">
                            R$ {{ number_format($saldoDisponivel, 2, ',', '.') }}
                        </p>
                        <p class="text-xs text-green-200 mt-1">Para saque</p>
                    </div>
                    <div class="text-white opacity-80">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Pago -->
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">Total Pago</p>
                        <p class="text-2xl font-bold text-white mt-2">
                            R$ {{ number_format($totalPago, 2, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Comissões pagas</p>
                    </div>
                    <div class="text-green-500">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pendente -->
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">Pendente</p>
                        <p class="text-2xl font-bold text-yellow-500 mt-2">
                            R$ {{ number_format($totalPendente, 2, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Em análise</p>
                    </div>
                    <div class="text-yellow-500">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Rejeitado -->
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">Rejeitado</p>
                        <p class="text-2xl font-bold text-red-500 mt-2">
                            R$ {{ number_format($totalRejeitado, 2, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Não aprovado</p>
                    </div>
                    <div class="text-red-500">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botão Solicitar Saque -->
        <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-white">Solicitar Novo Saque</h3>
                    <p class="text-sm text-gray-400 mt-1">
                        Saldo disponível: <span class="font-semibold text-green-500">R$ {{ number_format($saldoDisponivel, 2, ',', '.') }}</span>
                    </p>
                </div>
                @if($saldoDisponivel >= 50)
                    <button class="bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition-colors flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <span>Solicitar Saque</span>
                    </button>
                @else
                    <div class="text-right">
                        <p class="text-sm text-gray-400">Saldo mínimo: R$ 50,00</p>
                        <p class="text-xs text-gray-500 mt-1">Continue indicando para liberar saques</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tabela de Pagamentos -->
        <div class="bg-gray-900 border border-gray-800 rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800">
                <h3 class="text-lg font-semibold text-white">Histórico de Saques</h3>
            </div>
            
            @if($payments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Data
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Valor Solicitado
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Chave PIX
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Comprovante
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            @foreach($payments as $payment)
                                <tr class="hover:bg-gray-800 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ \Carbon\Carbon::parse($payment->created_at)->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="text-sm font-semibold text-white">
                                            R$ {{ number_format($payment->amount_display ?? $payment->amount, 2, ',', '.') }}
                                        </p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="text-sm text-gray-300">{{ $payment->pix_key }}</p>
                                            <p class="text-xs text-gray-500">{{ ucfirst($payment->pix_type) }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($payment->status == 'paid')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900 text-green-300">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                                Pago
                                            </span>
                                        @elseif($payment->status == 'pending')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-900 text-yellow-300">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                                </svg>
                                                Pendente
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-900 text-red-300">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                </svg>
                                                Rejeitado
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if($payment->proof)
                                            <a href="{{ $payment->proof }}" target="_blank" class="text-blue-400 hover:text-blue-300">
                                                Ver comprovante
                                            </a>
                                        @else
                                            <span class="text-gray-500">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-300">Nenhum saque realizado</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Seus saques aparecerão aqui após a primeira solicitação.
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>