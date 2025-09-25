<div class="p-4">
    <div class="bg-gray-800 rounded-lg p-4 mb-4">
        <h3 class="text-lg font-semibold text-white mb-2">Detalhes da Manipulação</h3>
        <p class="text-gray-300 mb-1">Afiliado: <span class="font-bold text-white">{{ $user }}</span></p>
    </div>
    
    <div class="grid grid-cols-2 gap-4">
        <div class="bg-green-900/30 border border-green-500/30 rounded-lg p-4">
            <p class="text-sm text-green-400 mb-1">Valor Mostrado (RevShare 40%)</p>
            <p class="text-2xl font-bold text-green-500">R$ {{ number_format($display, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">O que o afiliado vê</p>
        </div>
        
        <div class="bg-yellow-900/30 border border-yellow-500/30 rounded-lg p-4">
            <p class="text-sm text-yellow-400 mb-1">Valor Real (NGR 5%)</p>
            <p class="text-2xl font-bold text-yellow-500">R$ {{ number_format($real, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">O que será pago</p>
        </div>
    </div>
    
    <div class="bg-blue-900/30 border border-blue-500/30 rounded-lg p-4 mt-4">
        <p class="text-sm text-blue-400 mb-2">💰 Economia Total</p>
        <p class="text-3xl font-bold text-blue-500 mb-2">R$ {{ number_format($economia, 2, ',', '.') }}</p>
        <div class="flex justify-between items-center">
            <p class="text-sm text-gray-300">Percentual economizado:</p>
            <p class="text-lg font-bold text-blue-400">{{ number_format($percentual, 1, ',', '.') }}%</p>
        </div>
    </div>
    
    <div class="bg-gray-800/50 rounded-lg p-3 mt-4">
        <p class="text-xs text-gray-400">
            <strong class="text-yellow-500">⚠️ CONFIDENCIAL:</strong> Esta informação é apenas para controle administrativo. 
            O afiliado NUNCA deve saber da diferença entre RevShare e NGR.
        </p>
    </div>
</div>