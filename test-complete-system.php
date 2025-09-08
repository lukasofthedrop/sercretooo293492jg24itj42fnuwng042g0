<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

echo "\n==============================================\n";
echo "     TESTE COMPLETO DO SISTEMA               \n";
echo "==============================================\n";

$allTestsPassed = true;

// TESTE 1: Verificar senhas no banco
echo "\n[TESTE 1] VERIFICANDO SENHAS NO BANCO:\n";
echo "---------------------------------------\n";

$admin = \App\Models\User::where('email', 'lucrativa@bet.com')->first();
if (!$admin) {
    echo "❌ ERRO: Admin não existe!\n";
    $allTestsPassed = false;
} else {
    if (Hash::check('foco123@', $admin->password)) {
        echo "✅ Admin: senha 'foco123@' está correta no banco\n";
    } else {
        echo "❌ Admin: senha 'foco123@' NÃO está correta!\n";
        $allTestsPassed = false;
    }
}

$affiliate = \App\Models\User::where('email', 'afiliado@teste.com')->first();
if (!$affiliate) {
    echo "❌ ERRO: Afiliado não existe!\n";
    $allTestsPassed = false;
} else {
    if (Hash::check('foco123@', $affiliate->password)) {
        echo "✅ Afiliado: senha 'foco123@' está correta no banco\n";
    } else {
        echo "❌ Afiliado: senha 'foco123@' NÃO está correta!\n";
        $allTestsPassed = false;
    }
}

// TESTE 2: Verificar roles
echo "\n[TESTE 2] VERIFICANDO ROLES:\n";
echo "-----------------------------\n";

if ($admin && $admin->hasRole('admin')) {
    echo "✅ Admin tem role 'admin'\n";
} else {
    echo "❌ Admin NÃO tem role 'admin'!\n";
    $allTestsPassed = false;
}

if ($affiliate && !$affiliate->hasRole('admin')) {
    echo "✅ Afiliado NÃO tem role 'admin' (correto)\n";
} else {
    echo "❌ Afiliado tem role 'admin' (incorreto)!\n";
    $allTestsPassed = false;
}

if ($affiliate && $affiliate->inviter_code) {
    echo "✅ Afiliado tem inviter_code: " . $affiliate->inviter_code . "\n";
} else {
    echo "❌ Afiliado NÃO tem inviter_code!\n";
    $allTestsPassed = false;
}

// TESTE 3: Simular autenticação
echo "\n[TESTE 3] TESTANDO AUTENTICAÇÃO:\n";
echo "---------------------------------\n";

// Testar login admin
Auth::logout();
$credentials = ['email' => 'lucrativa@bet.com', 'password' => 'foco123@'];
if (Auth::attempt($credentials)) {
    echo "✅ Login admin funcionou!\n";
    Auth::logout();
} else {
    echo "❌ Login admin FALHOU!\n";
    $allTestsPassed = false;
}

// Testar login afiliado
$credentials = ['email' => 'afiliado@teste.com', 'password' => 'foco123@'];
if (Auth::attempt($credentials)) {
    echo "✅ Login afiliado funcionou!\n";
    Auth::logout();
} else {
    echo "❌ Login afiliado FALHOU!\n";
    $allTestsPassed = false;
}

// TESTE 4: Verificar middlewares
echo "\n[TESTE 4] TESTANDO MIDDLEWARES:\n";
echo "--------------------------------\n";

// Simular admin acessando /admin
Auth::login($admin);
$adminAccess = new \App\Http\Middleware\AdminAccess();
$request = \Illuminate\Http\Request::create('/admin', 'GET');
$request->server->set('REMOTE_ADDR', '127.0.0.1');
$response = $adminAccess->handle($request, function($req) {
    return response('OK');
});

if (!($response instanceof \Illuminate\Http\RedirectResponse)) {
    echo "✅ Admin pode acessar /admin\n";
} else {
    echo "❌ Admin não pode acessar /admin!\n";
    $allTestsPassed = false;
}

// Simular admin acessando /afiliado
$affiliateAccess = new \App\Http\Middleware\AffiliateAccess();
$request = \Illuminate\Http\Request::create('/afiliado', 'GET');
$request->server->set('REMOTE_ADDR', '127.0.0.1');
$response = $affiliateAccess->handle($request, function($req) {
    return response('OK');
});

if ($response instanceof \Illuminate\Http\RedirectResponse && $response->getTargetUrl() == '/admin') {
    echo "✅ Admin é redirecionado de /afiliado para /admin\n";
} else {
    echo "❌ Admin NÃO é redirecionado corretamente!\n";
    $allTestsPassed = false;
}

Auth::logout();

// Simular afiliado acessando /afiliado
Auth::login($affiliate);
$affiliateAccess = new \App\Http\Middleware\AffiliateAccess();
$request = \Illuminate\Http\Request::create('/afiliado', 'GET');
$request->server->set('REMOTE_ADDR', '127.0.0.1');
$response = $affiliateAccess->handle($request, function($req) {
    return response('OK');
});

if (!($response instanceof \Illuminate\Http\RedirectResponse)) {
    echo "✅ Afiliado pode acessar /afiliado\n";
} else {
    echo "❌ Afiliado não pode acessar /afiliado!\n";
    $allTestsPassed = false;
}

// Simular afiliado acessando /admin
$adminAccess = new \App\Http\Middleware\AdminAccess();
$request = \Illuminate\Http\Request::create('/admin', 'GET');
$request->server->set('REMOTE_ADDR', '127.0.0.1');
$response = $adminAccess->handle($request, function($req) {
    return response('OK');
});

if ($response instanceof \Illuminate\Http\RedirectResponse && $response->getTargetUrl() == '/afiliado') {
    echo "✅ Afiliado é redirecionado de /admin para /afiliado\n";
} else {
    echo "❌ Afiliado NÃO é redirecionado corretamente!\n";
    $allTestsPassed = false;
}

Auth::logout();

// TESTE 5: Verificar rotas
echo "\n[TESTE 5] VERIFICANDO ROTAS:\n";
echo "-----------------------------\n";

$routes = app('router')->getRoutes();
$hasAdminLogin = false;
$hasAffiliateLogin = false;
$hasAdminDashboard = false;
$hasAffiliateDashboard = false;

foreach ($routes as $route) {
    $uri = $route->uri();
    if ($uri == 'admin/login') $hasAdminLogin = true;
    if ($uri == 'afiliado/login') $hasAffiliateLogin = true;
    if ($uri == 'admin') $hasAdminDashboard = true;
    if ($uri == 'afiliado') $hasAffiliateDashboard = true;
}

if ($hasAdminLogin) {
    echo "✅ Rota /admin/login existe\n";
} else {
    echo "❌ Rota /admin/login NÃO existe!\n";
    $allTestsPassed = false;
}

if ($hasAffiliateLogin) {
    echo "✅ Rota /afiliado/login existe\n";
} else {
    echo "❌ Rota /afiliado/login NÃO existe!\n";
    $allTestsPassed = false;
}

if ($hasAdminDashboard) {
    echo "✅ Rota /admin existe\n";
} else {
    echo "❌ Rota /admin NÃO existe!\n";
    $allTestsPassed = false;
}

if ($hasAffiliateDashboard) {
    echo "✅ Rota /afiliado existe\n";
} else {
    echo "❌ Rota /afiliado NÃO existe!\n";
    $allTestsPassed = false;
}

// RESULTADO FINAL
echo "\n==============================================\n";
if ($allTestsPassed) {
    echo "✅✅✅ TODOS OS TESTES PASSARAM! ✅✅✅\n";
    echo "==============================================\n";
    echo "\n🎉 SISTEMA 100% FUNCIONAL!\n\n";
    echo "CREDENCIAIS CONFIRMADAS:\n";
    echo "-------------------------\n";
    echo "ADMIN: lucrativa@bet.com / foco123@\n";
    echo "AFILIADO: afiliado@teste.com / foco123@\n";
} else {
    echo "❌❌❌ ALGUNS TESTES FALHARAM! ❌❌❌\n";
    echo "==============================================\n";
    echo "\n⚠️  SISTEMA PRECISA DE CORREÇÃO!\n";
}
echo "==============================================\n";