<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;

echo "\n==============================================\n";
echo "     TESTE DO SISTEMA DE LOGOUT              \n";
echo "==============================================\n";

// Verificar rotas criadas
echo "\n[1] VERIFICANDO ROTAS:\n";
echo "----------------------\n";

$routes = app('router')->getRoutes();
$logoutRoutes = [
    'logout-completo' => false,
    'escolher-painel' => false,
    'admin/logout' => false,
    'afiliado/logout' => false
];

foreach ($routes as $route) {
    $uri = $route->uri();
    if (isset($logoutRoutes[$uri])) {
        $logoutRoutes[$uri] = true;
    }
}

foreach ($logoutRoutes as $route => $exists) {
    if ($exists) {
        echo "✅ Rota /{$route} existe\n";
    } else {
        echo "❌ Rota /{$route} NÃO existe\n";
    }
}

// Verificar controller e views
echo "\n[2] VERIFICANDO ARQUIVOS:\n";
echo "-------------------------\n";

if (file_exists(app_path('Http/Controllers/LogoutController.php'))) {
    echo "✅ LogoutController existe\n";
} else {
    echo "❌ LogoutController NÃO existe\n";
}

if (file_exists(resource_path('views/auth/escolher-painel.blade.php'))) {
    echo "✅ View escolher-painel existe\n";
} else {
    echo "❌ View escolher-painel NÃO existe\n";
}

// Simular processo de logout
echo "\n[3] SIMULANDO LOGOUT:\n";
echo "---------------------\n";

// Login como afiliado
$affiliate = \App\Models\User::where('email', 'afiliado@teste.com')->first();
if ($affiliate) {
    Auth::login($affiliate);
    if (Auth::check()) {
        echo "✅ Logado como afiliado: " . Auth::user()->email . "\n";
        
        // Simular logout
        Auth::logout();
        
        if (!Auth::check()) {
            echo "✅ Logout realizado com sucesso\n";
        } else {
            echo "❌ Logout falhou - ainda logado\n";
        }
    }
}

echo "\n==============================================\n";
echo "           SOLUÇÃO IMPLEMENTADA              \n";
echo "==============================================\n";
echo "\n📋 COMO FUNCIONA:\n";
echo "-----------------\n";
echo "1. Botão 'Trocar Painel' → Vai para /escolher-painel\n";
echo "2. Botão 'Sair' → Faz logout completo\n";
echo "3. Página de escolha → Permite escolher Admin ou Afiliado\n";
echo "\n💡 BOTÕES ADICIONADOS:\n";
echo "----------------------\n";
echo "✅ Dashboard Afiliado: Canto superior direito\n";
echo "   - Botão vermelho: 'Trocar Painel'\n";
echo "   - Botão cinza: 'Sair'\n";
echo "\n🔄 FLUXO:\n";
echo "---------\n";
echo "Afiliado → Trocar Painel → Escolher → Admin/Afiliado\n";
echo "Admin → Logout → Escolher → Admin/Afiliado\n";
echo "\n==============================================\n";