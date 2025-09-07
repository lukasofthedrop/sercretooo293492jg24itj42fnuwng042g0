<?php

use App\Models\User;
use App\Models\AffiliateSettings;
use App\Services\AffiliateMetricsService;

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=====================================\n";
echo "TESTE DO SISTEMA DE MANIPULAÇÃO\n";
echo "=====================================\n\n";

// Encontra o afiliado de teste
$affiliate = User::where('email', 'afiliado@teste.com')->first();

if (!$affiliate) {
    echo "❌ Afiliado de teste não encontrado. Execute create-affiliate-test.php primeiro.\n";
    exit(1);
}

echo "✅ Afiliado encontrado: {$affiliate->name} ({$affiliate->email})\n\n";

// Pega as configurações do afiliado
$settings = AffiliateSettings::getOrCreateForUser($affiliate->id);

echo "CONFIGURAÇÕES ATUAIS:\n";
echo "---------------------\n";
echo "RevShare REAL (usado em cálculos): {$settings->revshare_percentage}%\n";
echo "RevShare FAKE (mostrado ao afiliado): {$settings->revshare_display}%\n";
echo "CPA: R$ {$settings->cpa_value}\n";
echo "NGR Mínimo: R$ {$settings->ngr_minimum}\n";
echo "Tier: {$settings->tier}\n";
echo "Ativo: " . ($settings->is_active ? 'Sim' : 'Não') . "\n\n";

// Testa a manipulação - Define valores diferentes
echo "TESTE DE MANIPULAÇÃO:\n";
echo "---------------------\n";
echo "Atualizando valores para testar manipulação...\n";

$settings->update([
    'revshare_percentage' => 10.00,  // Valor REAL - 10%
    'revshare_display' => 30.00,      // Valor FAKE - 30%
]);

echo "✅ Valores atualizados:\n";
echo "   - RevShare REAL: 10%\n";
echo "   - RevShare DISPLAY: 30%\n\n";

// Simula o que o afiliado vê via API
echo "SIMULAÇÃO DA API (O que o afiliado vê):\n";
echo "----------------------------------------\n";

$metrics = AffiliateMetricsService::getAffiliateMetrics($affiliate->id);

echo "RevShare que aparece para o afiliado: {$metrics['revshare_display']}%\n";
echo "RevShare REAL (interno): {$metrics['revshare_percentage']}%\n\n";

// Teste de cálculo de comissão
echo "TESTE DE CÁLCULO DE COMISSÃO:\n";
echo "------------------------------\n";

$baseValue = 1000; // R$ 1.000 de base para cálculo
echo "Valor base para cálculo: R$ " . number_format($baseValue, 2, ',', '.') . "\n";

$commission = AffiliateMetricsService::calculateCommission($affiliate->id, 'revshare', $baseValue);
echo "Comissão calculada (usando valor REAL de 10%): R$ " . number_format($commission, 2, ',', '.') . "\n";
echo "Se usasse o valor FAKE (30%), seria: R$ " . number_format(($baseValue * 30 / 100), 2, ',', '.') . "\n\n";

// Verificação final
echo "VERIFICAÇÃO FINAL:\n";
echo "------------------\n";

if ($commission == ($baseValue * $settings->revshare_percentage / 100)) {
    echo "✅ SUCESSO! O sistema está usando o valor REAL para cálculos\n";
    echo "✅ O afiliado vê {$settings->revshare_display}% mas recebe baseado em {$settings->revshare_percentage}%\n";
} else {
    echo "❌ ERRO! O cálculo não está correto\n";
}

echo "\n=====================================\n";
echo "RESUMO DA MANIPULAÇÃO:\n";
echo "=====================================\n";
echo "1. Admin configura dois valores:\n";
echo "   - NGR Real (revshare_percentage): Usado em TODOS os cálculos\n";
echo "   - RevShare Visível (revshare_display): Mostrado ao afiliado\n";
echo "2. Afiliado vê apenas o valor FAKE na dashboard\n";
echo "3. Sistema calcula comissões com valor REAL\n";
echo "4. Afiliado não tem conhecimento da manipulação\n";
echo "\n✅ Sistema de manipulação implementado com sucesso!\n";