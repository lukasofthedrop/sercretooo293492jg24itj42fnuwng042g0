<?php

/**
 * VERIFICAÇÃO DO SISTEMA - LUCRATIVABET
 * Verifica o estado atual do sistema
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "\n========================================\n";
echo "  VERIFICAÇÃO DO SISTEMA LUCRATIVABET\n";
echo "========================================\n\n";

// Status do banco de dados
echo "📊 STATUS DO BANCO DE DADOS:\n";
echo "============================\n";

$stats = [
    'Usuários totais' => DB::table('users')->count(),
    'Usuários admins' => DB::table('users')->whereIn('email', ['admin@admin.com', 'admin@lucrativabet.com'])->count(),
    'Depósitos' => DB::table('deposits')->count(),
    'Depósitos aprovados' => DB::table('deposits')->where('status', '1')->count(),
    'Apostas' => DB::table('orders')->where('type', 'bet')->count(),
    'Saques' => DB::table('withdrawals')->count(),
    'Carteiras' => DB::table('wallets')->count(),
];

foreach ($stats as $label => $value) {
    echo str_pad($label . ':', 25) . $value . "\n";
}

// Valores financeiros
echo "\n💰 VALORES FINANCEIROS:\n";
echo "=======================\n";

$financial = [
    'Total depositado' => DB::table('deposits')->where('status', '1')->sum('amount'),
    'Total em carteiras' => DB::table('wallets')->sum('balance'),
    'Total apostado' => DB::table('orders')->where('type', 'bet')->sum('amount'),
    'Total sacado' => DB::table('withdrawals')->where('status', '1')->sum('amount'),
];

foreach ($financial as $label => $value) {
    echo str_pad($label . ':', 25) . 'R$ ' . number_format($value, 2, ',', '.') . "\n";
}

// Status do sistema
echo "\n🔧 STATUS DO SISTEMA:\n";
echo "=====================\n";

// Verificar cache
$cacheStatus = \Cache::get('test_cache', false);
\Cache::put('test_cache', true, 1);
echo "Cache: " . (\Cache::get('test_cache') ? '✅ Funcionando' : '❌ Com problemas') . "\n";

// Verificar database
try {
    DB::connection()->getPdo();
    echo "Banco de dados: ✅ Conectado\n";
} catch (\Exception $e) {
    echo "Banco de dados: ❌ Erro de conexão\n";
}

// Verificar modo
$appEnv = config('app.env');
$appDebug = config('app.debug');
echo "Ambiente: " . $appEnv . "\n";
echo "Debug: " . ($appDebug ? 'Ativado' : 'Desativado') . "\n";

// Backups disponíveis
echo "\n📁 BACKUPS DISPONÍVEIS:\n";
echo "======================\n";

$backupDir = storage_path('backups');
if (file_exists($backupDir)) {
    $backups = array_diff(scandir($backupDir), ['.', '..']);
    if (empty($backups)) {
        echo "Nenhum backup encontrado.\n";
    } else {
        foreach ($backups as $backup) {
            if (is_dir($backupDir . '/' . $backup)) {
                echo "  - " . $backup . "\n";
            }
        }
    }
} else {
    echo "Diretório de backups não existe.\n";
}

echo "\n========================================\n\n";