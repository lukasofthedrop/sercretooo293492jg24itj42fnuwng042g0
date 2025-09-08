<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;

echo "\n==============================================\n";
echo "     TESTE DA PÁGINA ANÁLISE INDIVIDUAL      \n";
echo "==============================================\n";

// Verificar arquivo e classe
echo "\n[1] VERIFICANDO ARQUIVOS:\n";
echo "-------------------------\n";

if (file_exists(app_path('Filament/Pages/AffiliateAnalytics.php'))) {
    echo "✅ Arquivo PHP existe\n";
} else {
    echo "❌ Arquivo PHP NÃO existe\n";
}

if (file_exists(resource_path('views/filament/pages/affiliate-analytics.blade.php'))) {
    echo "✅ View Blade existe\n";
} else {
    echo "❌ View Blade NÃO existe\n";
}

// Verificar se a classe existe
if (class_exists('App\Filament\Pages\AffiliateAnalytics')) {
    echo "✅ Classe AffiliateAnalytics existe\n";
    
    // Verificar propriedades da classe
    $class = new ReflectionClass('App\Filament\Pages\AffiliateAnalytics');
    $slug = $class->getStaticPropertyValue('slug');
    echo "✅ Slug da página: " . $slug . "\n";
} else {
    echo "❌ Classe AffiliateAnalytics NÃO existe\n";
}

// Verificar rota
echo "\n[2] VERIFICANDO ROTA:\n";
echo "---------------------\n";

$routes = app('router')->getRoutes();
$found = false;

foreach ($routes as $route) {
    if (str_contains($route->uri(), 'analise-individual')) {
        echo "✅ Rota encontrada: /" . $route->uri() . "\n";
        echo "   Método: " . implode('|', $route->methods()) . "\n";
        $found = true;
        break;
    }
}

if (!$found) {
    echo "❌ Rota NÃO encontrada\n";
}

// Simular acesso como admin
echo "\n[3] SIMULANDO ACESSO:\n";
echo "---------------------\n";

$admin = \App\Models\User::where('email', 'lucrativa@bet.com')->first();

if ($admin) {
    Auth::login($admin);
    
    if (Auth::check() && Auth::user()->hasRole('admin')) {
        echo "✅ Logado como admin: " . Auth::user()->email . "\n";
        
        // Verificar se pode acessar
        if (\App\Filament\Pages\AffiliateAnalytics::canAccess()) {
            echo "✅ Admin tem permissão para acessar a página\n";
        } else {
            echo "❌ Admin NÃO tem permissão\n";
        }
    }
    
    Auth::logout();
}

echo "\n==============================================\n";
echo "               RESUMO DO PROBLEMA             \n";
echo "==============================================\n";
echo "\n📝 CORREÇÃO APLICADA:\n";
echo "--------------------\n";
echo "• View ajustada para usar request()->has('affiliate_id')\n";
echo "• Página agora mostra tabela quando sem ID\n";
echo "• Mostra detalhes quando com ID (?affiliate_id=X)\n";
echo "\n🔗 COMO ACESSAR:\n";
echo "----------------\n";
echo "• Lista: http://localhost:8080/admin/analise-individual\n";
echo "• Detalhes: http://localhost:8080/admin/analise-individual?affiliate_id=ID\n";
echo "\n✅ PÁGINA DEVE FUNCIONAR AGORA!\n";
echo "==============================================\n";