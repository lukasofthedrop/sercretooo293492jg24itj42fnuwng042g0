<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Cards de Estatísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Total de Usuários -->
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">Total de Indicados</p>
                        <p class="text-3xl font-bold text-white mt-2">{{ $totalUsers }}</p>
                    </div>
                    <div class="text-blue-500">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Usuários Ativos -->
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">Usuários Ativos</p>
                        <p class="text-3xl font-bold text-green-500 mt-2">{{ $activeUsers }}</p>
                        <p class="text-xs text-gray-500 mt-1">Com depósitos</p>
                    </div>
                    <div class="text-green-500">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total em Depósitos -->
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">Total Depositado</p>
                        <p class="text-2xl font-bold text-white mt-2">
                            R$ {{ number_format($totalDeposits, 2, ',', '.') }}
                        </p>
                    </div>
                    <div class="text-green-500">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Taxa de Conversão -->
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">Taxa de Conversão</p>
                        <p class="text-3xl font-bold text-yellow-500 mt-2">
                            {{ number_format($conversionRate, 1) }}%
                        </p>
                    </div>
                    <div class="text-yellow-500">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Link de Indicação -->
        <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Seu Link de Indicação</h3>
            <div class="flex items-center space-x-4">
                <div class="flex-1">
                    <div class="bg-gray-800 rounded-lg px-4 py-3 font-mono text-sm text-green-400">
                        {{ url('/register?code=' . $userCode) }}
                    </div>
                </div>
                <button onclick="navigator.clipboard.writeText('{{ url('/register?code=' . $userCode) }}')" 
                        class="bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition-colors">
                    Copiar Link
                </button>
            </div>
        </div>

        <!-- Tabela de Usuários -->
        <div class="bg-gray-900 border border-gray-800 rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800">
                <h3 class="text-lg font-semibold text-white">Lista de Usuários Indicados</h3>
            </div>
            
            @if($referredUsers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Usuário
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Data Cadastro
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Total Depositado
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Qtd Depósitos
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Saldo Carteira
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            @foreach($referredUsers as $referredUser)
                                <tr class="hover:bg-gray-800 transition-colors">
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="text-sm font-medium text-white">{{ $referredUser->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $referredUser->email }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ $referredUser->created_at->format('d/m/Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-white">
                                        R$ {{ number_format($referredUser->total_depositos ?? 0, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ $referredUser->qtd_depositos ?? 0 }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        R$ {{ number_format($referredUser->wallet->balance ?? 0, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($referredUser->total_depositos > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900 text-green-300">
                                                Ativo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-700 text-gray-300">
                                                Inativo
                                            </span>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-300">Nenhum usuário indicado ainda</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Compartilhe seu link de indicação para começar a ganhar comissões.
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>