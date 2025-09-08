<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

echo "\n==============================================\n";
echo "     VALIDAÇÃO FINAL DO SISTEMA              \n";
echo "==============================================\n";

$testsOK = 0;
$testsFailed = 0;

// TESTE 1: Verificar usuários existem
echo "\n[1] USUÁRIOS:\n";
echo "-------------\n";

$admin = \App\Models\User::where('email', 'lucrativa@bet.com')->first();
$affiliate = \App\Models\User::where('email', 'afiliado@teste.com')->first();

if ($admin) {
    echo "✅ Admin existe: lucrativa@bet.com\n";
    $testsOK++;
} else {
    echo "❌ Admin não existe\n";
    $testsFailed++;
}

if ($affiliate) {
    echo "✅ Afiliado existe: afiliado@teste.com\n";
    $testsOK++;
} else {
    echo "❌ Afiliado não existe\n";
    $testsFailed++;
}

// TESTE 2: Verificar senhas
echo "\n[2] SENHAS:\n";
echo "-----------\n";

if ($admin && Hash::check('foco123@', $admin->password)) {
    echo "✅ Senha admin: foco123@ funciona\n";
    $testsOK++;
} else {
    echo "❌ Senha admin incorreta\n";
    $testsFailed++;
}

if ($affiliate && Hash::check('foco123@', $affiliate->password)) {
    echo "✅ Senha afiliado: foco123@ funciona\n";
    $testsOK++;
} else {
    echo "❌ Senha afiliado incorreta\n";
    $testsFailed++;
}

// TESTE 3: Verificar roles e inviter_code
echo "\n[3] ROLES E IDENTIFICAÇÃO:\n";
echo "---------------------------\n";

if ($admin && $admin->hasRole('admin')) {
    echo "✅ Admin tem role 'admin'\n";
    $testsOK++;
} else {
    echo "❌ Admin sem role 'admin'\n";
    $testsFailed++;
}

if ($affiliate && !$affiliate->hasRole('admin')) {
    echo "✅ Afiliado NÃO tem role 'admin'\n";
    $testsOK++;
} else {
    echo "❌ Afiliado tem role 'admin' (erro)\n";
    $testsFailed++;
}

if ($affiliate && $affiliate->inviter_code) {
    echo "✅ Afiliado tem inviter_code: {$affiliate->inviter_code}\n";
    $testsOK++;
} else {
    echo "❌ Afiliado sem inviter_code\n";
    $testsFailed++;
}

// TESTE 4: Login funciona
echo "\n[4] AUTENTICAÇÃO:\n";
echo "-----------------\n";

Auth::logout();
if (Auth::attempt(['email' => 'lucrativa@bet.com', 'password' => 'foco123@'])) {
    echo "✅ Login admin funciona\n";
    $testsOK++;
    Auth::logout();
} else {
    echo "❌ Login admin falhou\n";
    $testsFailed++;
}

if (Auth::attempt(['email' => 'afiliado@teste.com', 'password' => 'foco123@'])) {
    echo "✅ Login afiliado funciona\n";
    $testsOK++;
    Auth::logout();
} else {
    echo "❌ Login afiliado falhou\n";
    $testsFailed++;
}

// TESTE 5: Rotas existem
echo "\n[5] ROTAS:\n";
echo "----------\n";

$routes = app('router')->getRoutes();
$requiredRoutes = [
    'admin/login' => false,
    'afiliado/login' => false,
    'admin' => false,
    'afiliado' => false
];

foreach ($routes as $route) {
    $uri = $route->uri();
    if (isset($requiredRoutes[$uri])) {
        $requiredRoutes[$uri] = true;
    }
}

foreach ($requiredRoutes as $route => $exists) {
    if ($exists) {
        echo "✅ Rota /{$route} existe\n";
        $testsOK++;
    } else {
        echo "❌ Rota /{$route} não existe\n";
        $testsFailed++;
    }
}

// TESTE 6: Middlewares configurados
echo "\n[6] MIDDLEWARES:\n";
echo "----------------\n";

if (class_exists('\App\Http\Middleware\AdminAccess')) {
    echo "✅ AdminAccess middleware existe\n";
    $testsOK++;
} else {
    echo "❌ AdminAccess middleware não existe\n";
    $testsFailed++;
}

if (class_exists('\App\Http\Middleware\AffiliateAccess')) {
    echo "✅ AffiliateAccess middleware existe\n";
    $testsOK++;
} else {
    echo "❌ AffiliateAccess middleware não existe\n";
    $testsFailed++;
}

// TESTE 7: Providers registrados
echo "\n[7] PROVIDERS:\n";
echo "--------------\n";

$providers = config('app.providers');
if (in_array('App\Providers\Filament\AdminPanelProvider', $providers)) {
    echo "✅ AdminPanelProvider registrado\n";
    $testsOK++;
} else {
    echo "❌ AdminPanelProvider não registrado\n";
    $testsFailed++;
}

if (in_array('App\Providers\Filament\AffiliatePanelProvider', $providers)) {
    echo "✅ AffiliatePanelProvider registrado\n";
    $testsOK++;
} else {
    echo "❌ AffiliatePanelProvider não registrado\n";
    $testsFailed++;
}

// RESULTADO FINAL
echo "\n==============================================\n";
echo "               RESULTADO FINAL                \n";
echo "==============================================\n";
echo "✅ Testes OK: {$testsOK}\n";
echo "❌ Testes Falhados: {$testsFailed}\n";
echo "----------------------------------------------\n";

if ($testsFailed == 0) {
    echo "🎉 SISTEMA 100% OPERACIONAL!\n";
    echo "==============================================\n";
    echo "\n📝 CREDENCIAIS FUNCIONANDO:\n";
    echo "----------------------------\n";
    echo "ADMIN: lucrativa@bet.com / foco123@\n";
    echo "  Acesso: http://localhost:8080/admin\n\n";
    echo "AFILIADO: afiliado@teste.com / foco123@\n";
    echo "  Acesso: http://localhost:8080/afiliado\n";
} else {
    echo "⚠️  SISTEMA COM PROBLEMAS\n";
    echo "Verifique os testes que falharam acima.\n";
}
echo "==============================================\n";