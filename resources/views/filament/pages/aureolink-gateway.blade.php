<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        AureoLink Gateway
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-1">
                        Configure e gerencie pagamentos via AureoLink PIX
                    </p>
                </div>
                <div class="flex space-x-3">
                    <a href="https://app.aureolink.com.br/docs/intro/first-steps#" 
                       target="_blank"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                        Documentação
                    </a>
                    <a href="https://app.aureolink.com.br/" 
                       target="_blank"
                       class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            @php $stats = $this->getStats(); @endphp
            
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 dark:bg-green-900 rounded-md">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total de Depósitos</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_deposits'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Aprovados: {{ $stats['approved_deposits'] }} | Pendentes: {{ $stats['pending_deposits'] }} | Falhas: {{ $stats['failed_deposits'] }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 dark:bg-orange-900 rounded-md">
                        <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total de Saques</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_withdrawals'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Aprovados: {{ $stats['approved_withdrawals'] }} | Pendentes: {{ $stats['pending_withdrawals'] }} | Falhas: {{ $stats['failed_withdrawals'] }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-md">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Volume de Entrada</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">R$ {{ number_format($stats['total_amount'] / 100, 2, ',', '.') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Saídas: R$ {{ number_format($stats['total_withdrawal_amount'] / 100, 2, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-md">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Usuários Ativos</p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['total_users'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Utilizaram AureoLink
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Resumo da API AureoLink -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Resumo da API AureoLink
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Dados obtidos diretamente da API AureoLink
                </p>
            </div>
            <div class="p-6">
                @php $apiTransactions = $this->getTransactions(); @endphp
                @if(count($apiTransactions) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600">{{ count($apiTransactions) }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Transações da API</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600">R$ {{ number_format(collect($apiTransactions)->sum('amount') / 100, 2, ',', '.') }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Volume Total (API)</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-purple-600">{{ collect($apiTransactions)->where('status', 'paid')->count() }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Transações Pagas (API)</p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <p class="text-gray-500 dark:text-gray-400">Configure suas credenciais para ver dados da API</p>
                    </div>
        @endif
            </div>
        </div>

        <!-- Configuration Section -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Configuração da Integração
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Configure suas credenciais da API AureoLink
                </p>
            </div>
            
            <div class="p-6">
                <form wire:submit.prevent="saveConfig" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Client ID
                            </label>
                            <input type="text" 
                                   wire:model="clientId"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="Digite o Client ID da AureoLink">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Client Secret
                            </label>
                            <input type="password" 
                                   wire:model="clientSecret"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                   placeholder="Digite o Client Secret da AureoLink">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Webhook URL
                        </label>
                        <input type="text" 
                               wire:model="webhookUrl"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                               placeholder="URL para receber notificações"
                               value="{{ url('/aureolink/webhook') }}">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Configure esta URL no painel da AureoLink para receber notificações de pagamento
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Chave de Saque
                        </label>
                        <input type="text" 
                               wire:model="withdrawKey"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                               placeholder="Digite a chave de saque da AureoLink">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Chave necessária para processar saques via API
                        </p>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" 
                               wire:model="isEnabled"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label class="ml-2 block text-sm text-gray-900 dark:text-gray-300">
                            Ativar Gateway AureoLink
                        </label>
                    </div>

                    <div class="flex space-x-3">
                        <button type="button"
                                wire:click="saveConfig"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Salvar Configuração
                        </button>
                        
                        <button type="button"
                                wire:click="testConnection"
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Testar Conexão
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Transações da Plataforma (AureoLink) -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Transações da Plataforma (AureoLink)
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Histórico completo de depósitos e saques processados via AureoLink
                </p>
            </div>
            <div class="p-6">
                @php $detailedTransactions = $this->getDetailedTransactions(); @endphp
                @if($detailedTransactions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Usuário</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Valor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Payment ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($detailedTransactions as $transaction)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            #{{ $transaction['id'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($transaction['type'] === 'deposit') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                @else bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200 @endif">
                                                @if($transaction['type'] === 'deposit')
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 12L6 8h8l-4 4z"/>
                                                    </svg>
                                                    Depósito
                                                @else
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 8l4 4H6l4-4z"/>
                                                    </svg>
                                                    Saque
                                                @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            {{ $transaction['user_name'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $transaction['user_email'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            R$ {{ number_format($transaction['amount'] / 100, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($transaction['status'] === 'paid') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                @elseif($transaction['status'] === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                                @if($transaction['status'] === 'paid')
                                                    ✓ Aprovado
                                                @elseif($transaction['status'] === 'pending')
                                                    ⏳ Pendente
                                                @else
                                                    ✗ {{ ucfirst($transaction['status']) }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400 font-mono">
                                            {{ $transaction['payment_id'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $transaction['created_at']->format('d/m/Y H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Mostrando as últimas 100 transações processadas via AureoLink
                        </p>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Nenhuma transação AureoLink encontrada</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            As transações aparecerão aqui quando os usuários começarem a usar o gateway AureoLink.
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Transações da API AureoLink (Raw) -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    Transações da API AureoLink (Dados Brutos)
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Dados obtidos diretamente da API AureoLink para debug e conferência
                </p>
            </div>
            <div class="p-6">
                @php $apiTransactions = $this->getTransactions(); @endphp
                @if(count($apiTransactions) > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID API</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Valor</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Data</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Detalhes</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($apiTransactions as $transaction)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-mono">
                                            {{ $transaction['id'] ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                            R$ {{ number_format(($transaction['amount'] ?? 0) / 100, 2, ',', '.') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if(($transaction['status'] ?? '') === 'paid') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                @elseif(($transaction['status'] ?? '') === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                                {{ ucfirst($transaction['status'] ?? 'Unknown') }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ isset($transaction['created_at']) ? \Carbon\Carbon::parse($transaction['created_at'])->format('d/m/Y H:i') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            <button class="text-blue-600 hover:text-blue-800 dark:text-blue-400" 
                                                    onclick="alert('{{ json_encode($transaction) }}')">
                                                Ver JSON
                                            </button>
                                        </td>
                                    </tr>
            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Nenhuma transação da API encontrada</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Configure suas credenciais e teste a conexão para ver as transações da API.</p>
                    </div>
        @endif
            </div>
        </div>
    </div>
</x-filament-panels::page> 