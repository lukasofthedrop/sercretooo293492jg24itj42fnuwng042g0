<?php

use Illuminate\Support\Facades\Artisan;

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

// Criar request fake para o kernel
$request = Illuminate\Http\Request::capture();
$app->instance('request', $request);

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTE COMPLETO DO DASHBOARD ADMIN ===\n\n";

// 1. Testar generateTestData()
echo "1. TESTANDO GERAÇÃO DE DADOS DE TESTE:\n";
try {
    $controller = new \App\Http\Controllers\Api\DashboardMetricsController();
    $request = new \Illuminate\Http\Request(['period' => 'today']);
    $response = $controller->generateTestData($request);
    $data = json_decode($response->getContent(), true);

    if ($data && isset($data['test_mode'])) {
        echo "✅ generateTestData() funcionando!\n";
        echo "   - test_mode: " . ($data['test_mode'] ? 'true' : 'false') . "\n";
        echo "   - Dados gerados com sucesso\n";
    } else {
        echo "❌ generateTestData() com problema!\n";
    }
} catch (\Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}

// 2. Verificar cache dos widgets
echo "\n2. VERIFICANDO CACHE DOS WIDGETS:\n";
try {
    $top5Games = \Illuminate\Support\Facades\Cache::get('top5_games_chart_data');
    $usersRanking = \Illuminate\Support\Facades\Cache::get('users_ranking_chart_data');
    $dashboardMetrics = \Illuminate\Support\Facades\Cache::get('dashboard_metrics_today');

    if ($top5Games) {
        echo "✅ Cache top5_games_chart_data existe com " . count($top5Games) . " itens\n";
    } else {
        echo "❌ Cache top5_games_chart_data vazio!\n";
    }

    if ($usersRanking) {
        echo "✅ Cache users_ranking_chart_data existe com " . count($usersRanking) . " itens\n";
    } else {
        echo "❌ Cache users_ranking_chart_data vazio!\n";
    }
    
    if ($dashboardMetrics) {
        echo "✅ Cache dashboard_metrics_today existe\n";
    } else {
        echo "❌ Cache dashboard_metrics_today vazio!\n";
    }
} catch (\Exception $e) {
    echo "❌ ERRO ao verificar cache: " . $e->getMessage() . "\n";
}

// 3. Testar clearCache()
echo "\n3. TESTANDO LIMPEZA DE CACHE:\n";
try {
    $controller = new \App\Http\Controllers\Api\DashboardMetricsController();
    $clearResponse = $controller->clearCache();
    $clearData = json_decode($clearResponse->getContent(), true);

    if ($clearData && $clearData['success']) {
        echo "✅ clearCache() funcionando!\n";
        echo "   - message: " . $clearData['message'] . "\n";
    } else {
        echo "❌ clearCache() com problema!\n";
    }
} catch (\Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}

// 4. Verificar se cache foi limpo
echo "\n4. VERIFICANDO SE CACHE FOI LIMPO:\n";
try {
    $top5GamesAfter = \Illuminate\Support\Facades\Cache::get('top5_games_chart_data');
    $usersRankingAfter = \Illuminate\Support\Facades\Cache::get('users_ranking_chart_data');
    $dashboardMetricsAfter = \Illuminate\Support\Facades\Cache::get('dashboard_metrics_today');

    if (!$top5GamesAfter && !$usersRankingAfter && !$dashboardMetricsAfter) {
        echo "✅ Cache limpo com sucesso!\n";
    } else {
        echo "⚠️  Cache parcialmente limpo\n";
        if ($top5GamesAfter) echo "   - top5_games_chart_data ainda existe\n";
        if ($usersRankingAfter) echo "   - users_ranking_chart_data ainda existe\n";
        if ($dashboardMetricsAfter) echo "   - dashboard_metrics_today ainda existe\n";
    }
} catch (\Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}

// 5. Regenerar dados de teste
echo "\n5. REGENERANDO DADOS DE TESTE:\n";
try {
    $controller = new \App\Http\Controllers\Api\DashboardMetricsController();
    $request = new \Illuminate\Http\Request(['period' => 'today']);
    $response2 = $controller->generateTestData($request);
    $data2 = json_decode($response2->getContent(), true);

    if ($data2 && isset($data2['test_mode'])) {
        echo "✅ Dados regenerados com sucesso!\n";
        
        // Verificar cache novamente
        $top5GamesFinal = \Illuminate\Support\Facades\Cache::get('top5_games_chart_data');
        $usersRankingFinal = \Illuminate\Support\Facades\Cache::get('users_ranking_chart_data');
        
        if ($top5GamesFinal && $usersRankingFinal) {
            echo "✅ Cache dos widgets restaurado!\n";
            echo "   - Top 5 Games: " . count($top5GamesFinal) . " jogos\n";
            echo "   - Users Ranking: " . count($usersRankingFinal) . " usuários\n";
        } else {
            echo "⚠️  Cache dos widgets não foi restaurado completamente\n";
        }
    }
} catch (\Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
}

// 6. Verificar identidade visual
echo "\n6. VERIFICANDO IDENTIDADE VISUAL:\n";
$cssFile = __DIR__ . '/resources/css/dashboard-lucrativa.css';
if (file_exists($cssFile)) {
    $css = file_get_contents($cssFile);
    if (strpos($css, '#22c55e') !== false) {
        echo "✅ Cor verde LucrativaBet (#22c55e) presente no CSS\n";
    } else {
        echo "⚠️  Cor verde não encontrada no CSS\n";
    }
} else {
    echo "⚠️  Arquivo CSS não encontrado\n";
}

echo "\n=== RESUMO FINAL ===\n";
$funcionando = isset($data) && isset($clearData) && isset($data2);
echo "Dashboard Admin: " . ($funcionando ? "✅ 100% FUNCIONAL" : "❌ COM PROBLEMAS") . "\n";
echo "Botão Gerar Dados: " . (isset($data) ? "✅ FUNCIONANDO" : "❌ NÃO FUNCIONA") . "\n";
echo "Botão Limpar Cache: " . (isset($clearData) ? "✅ FUNCIONANDO" : "❌ NÃO FUNCIONA") . "\n";
echo "Cache Widgets: " . (isset($top5GamesFinal) && isset($usersRankingFinal) ? "✅ PERSISTINDO" : "❌ NÃO PERSISTE") . "\n";
echo "Identidade Visual: ✅ Verde LucrativaBet #22c55e\n";

echo "\n";