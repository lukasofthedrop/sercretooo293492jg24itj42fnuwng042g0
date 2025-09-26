<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Cards de Resumo -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Total de Depósitos -->
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">Total em Depósitos</p>
                        <p class="text-2xl font-bold text-white mt-2">
                            R$ {{ number_format($totalDepositos, 2, ',', '.') }}
                        </p>
                    </div>
                    <div class="text-green-500">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Comissão Display (40% fake) -->
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">RevShare (40%)</p>
                        <p class="text-2xl font-bold text-green-500 mt-2">
                            R$ {{ number_format($totalComissaoDisplay, 2, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Comissão exibida</p>
                    </div>
                    <div class="text-green-500">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Taxa de Conversão -->
            <div class="bg-gray-900 border border-gray-800 rounded-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm font-medium">Total de Conversões</p>
                        <p class="text-2xl font-bold text-white mt-2">
                            {{ count($conversions) }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">Depósitos realizados</p>
                    </div>
                    <div class="text-blue-500">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações do Afiliado -->
        <div class="bg-gray-900 border border-gray-800 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div>
                        <p class="text-sm text-gray-400">Afiliado</p>
                        <p class="text-lg font-semibold text-white">{{ $userName }}</p>
                    </div>
                    <div class="border-l border-gray-700 pl-4">
                        <p class="text-sm text-gray-400">Código de Afiliado</p>
                        <p class="text-lg font-mono font-semibold text-green-500">{{ $userCode }}</p>
                    </div>
                </div>
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-900 text-green-300">
                        Ativo
                    </span>
                </div>
            </div>
        </div>

        <!-- Tabela de Conversões -->
        <div class="bg-gray-900 border border-gray-800 rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-800">
                <h3 class="text-lg font-semibold text-white">Histórico de Conversões</h3>
            </div>
            
            @if($conversions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Data
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Usuário
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Valor Depósito
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    RevShare (40%)
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            @foreach($conversions as $conversion)
                                <tr class="hover:bg-gray-800 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ \Carbon\Carbon::parse($conversion->created_at)->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="text-sm font-medium text-white">{{ $conversion->user_name }}</p>
                                            <p class="text-xs text-gray-400">{{ $conversion->user_email }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                        R$ {{ number_format($conversion->valor_deposito, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-500">
                                        R$ {{ number_format($conversion->comissao_display, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900 text-green-300">
                                            Confirmado
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-300">Nenhuma conversão ainda</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Suas conversões aparecerão aqui quando seus indicados fizerem depósitos.
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>