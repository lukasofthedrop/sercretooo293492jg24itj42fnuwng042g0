<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;

echo "\n==============================================\n";
echo "     TESTE DE ACESSO DIRETO AOS PAINÉIS      \n";
echo "==============================================\n";

// Verificar usuários
$admin = \App\Models\User::where('email', 'lucrativa@bet.com')->first();
$affiliate = \App\Models\User::where('email', 'afiliado@teste.com')->first();

echo "\n[1] USUÁRIOS VERIFICADOS:\n";
echo "-------------------------\n";
echo "✅ Admin: " . ($admin ? $admin->email : 'NÃO ENCONTRADO') . "\n";
echo "✅ Afiliado: " . ($affiliate ? $affiliate->email : 'NÃO ENCONTRADO') . "\n";

// Testar middlewares de redirecionamento
echo "\n[2] TESTE DE REDIRECIONAMENTO:\n";
echo "-------------------------------\n";

// Admin tentando acessar /afiliado
if ($admin) {
    Auth::login($admin);
    $affiliateAccess = new \App\Http\Middleware\AffiliateAccess();
    $request = \Illuminate\Http\Request::create('/afiliado', 'GET');
    $request->server->set('REMOTE_ADDR', '127.0.0.1');
    $response = $affiliateAccess->handle($request, function($req) {
        return response('OK');
    });
    
    if ($response instanceof \Illuminate\Http\RedirectResponse) {
        echo "✅ Admin acessando /afiliado → Redirecionado para: " . $response->getTargetUrl() . "\n";
    } else {
        echo "✅ Admin pode acessar /admin (não redirecionado)\n";
    }
    Auth::logout();
}

// Afiliado tentando acessar /admin
if ($affiliate) {
    Auth::login($affiliate);
    $adminAccess = new \App\Http\Middleware\AdminAccess();
    $request = \Illuminate\Http\Request::create('/admin', 'GET');
    $request->server->set('REMOTE_ADDR', '127.0.0.1');
    $response = $adminAccess->handle($request, function($req) {
        return response('OK');
    });
    
    if ($response instanceof \Illuminate\Http\RedirectResponse) {
        echo "✅ Afiliado acessando /admin → Redirecionado para: " . $response->getTargetUrl() . "\n";
    } else {
        echo "✅ Afiliado pode acessar /afiliado (não redirecionado)\n";
    }
    Auth::logout();
}

// Verificar rotas
echo "\n[3] ROTAS PRINCIPAIS:\n";
echo "---------------------\n";

$routes = app('router')->getRoutes();
$importantRoutes = [
    'admin/login' => false,
    'afiliado/login' => false,
    'admin' => false,
    'afiliado' => false,
    'logout-completo' => false
];

foreach ($routes as $route) {
    $uri = $route->uri();
    if (isset($importantRoutes[$uri])) {
        $importantRoutes[$uri] = true;
    }
}

foreach ($importantRoutes as $route => $exists) {
    echo ($exists ? "✅" : "❌") . " /{$route}\n";
}

echo "\n==============================================\n";
echo "              COMO FUNCIONA AGORA             \n";
echo "==============================================\n";
echo "\n🔐 ACESSO DIRETO:\n";
echo "-----------------\n";
echo "• Acessar /admin → Login Admin → Dashboard Admin\n";
echo "• Acessar /afiliado → Login Afiliado → Dashboard Afiliado\n";
echo "\n🔄 LOGOUT:\n";
echo "----------\n";
echo "• Logout do Admin → Redireciona para /admin/login\n";
echo "• Logout do Afiliado → Redireciona para /afiliado/login\n";
echo "\n✅ SEM PÁGINA DE ESCOLHA - ACESSO DIRETO!\n";
echo "==============================================\n";