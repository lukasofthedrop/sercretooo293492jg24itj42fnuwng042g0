<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n==============================================\n";
echo "     TESTE DE LOGIN NOS PAINÉIS              \n";
echo "==============================================\n";

// Testar login do admin
echo "\n🔐 TESTANDO LOGIN ADMIN:\n";
echo "------------------------\n";

$admin = \App\Models\User::where('email', 'lucrativa@bet.com')->first();

if ($admin) {
    // Simular login
    \Illuminate\Support\Facades\Auth::login($admin);
    
    if (\Illuminate\Support\Facades\Auth::check()) {
        echo "✅ Login simulado com sucesso!\n";
        echo "✅ Usuário: " . \Illuminate\Support\Facades\Auth::user()->email . "\n";
        echo "✅ É Admin?: " . (\Illuminate\Support\Facades\Auth::user()->hasRole('admin') ? 'SIM' : 'NÃO') . "\n";
        
        // Verificar redirecionamento
        $adminAccess = new \App\Http\Middleware\AdminAccess();
        $request = \Illuminate\Http\Request::create('/admin', 'GET');
        $request->server->set('REMOTE_ADDR', '127.0.0.1');
        $response = $adminAccess->handle($request, function($req) {
            return response('OK');
        });
        
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            echo "❌ Admin seria redirecionado para: " . $response->getTargetUrl() . "\n";
        } else {
            echo "✅ Admin pode acessar /admin\n";
        }
        
        // Verificar acesso ao /afiliado
        $affiliateAccess = new \App\Http\Middleware\AffiliateAccess();
        $request = \Illuminate\Http\Request::create('/afiliado', 'GET');
        $request->server->set('REMOTE_ADDR', '127.0.0.1');
        $response = $affiliateAccess->handle($request, function($req) {
            return response('OK');
        });
        
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            echo "✅ Admin é redirecionado de /afiliado para: " . $response->getTargetUrl() . "\n";
        } else {
            echo "❌ Admin NÃO seria redirecionado de /afiliado\n";
        }
    }
    
    \Illuminate\Support\Facades\Auth::logout();
}

// Testar login do afiliado
echo "\n🔐 TESTANDO LOGIN AFILIADO:\n";
echo "---------------------------\n";

$affiliate = \App\Models\User::where('email', 'afiliado@teste.com')->first();

if ($affiliate) {
    // Simular login
    \Illuminate\Support\Facades\Auth::login($affiliate);
    
    if (\Illuminate\Support\Facades\Auth::check()) {
        echo "✅ Login simulado com sucesso!\n";
        echo "✅ Usuário: " . \Illuminate\Support\Facades\Auth::user()->email . "\n";
        echo "✅ É Admin?: " . (\Illuminate\Support\Facades\Auth::user()->hasRole('admin') ? 'SIM' : 'NÃO') . "\n";
        echo "✅ Inviter Code: " . \Illuminate\Support\Facades\Auth::user()->inviter_code . "\n";
        
        // Verificar redirecionamento
        $adminAccess = new \App\Http\Middleware\AdminAccess();
        $request = \Illuminate\Http\Request::create('/admin', 'GET');
        $request->server->set('REMOTE_ADDR', '127.0.0.1');
        $response = $adminAccess->handle($request, function($req) {
            return response('OK');
        });
        
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            echo "✅ Afiliado é redirecionado de /admin para: " . $response->getTargetUrl() . "\n";
        } else {
            echo "❌ Afiliado NÃO seria redirecionado de /admin\n";
        }
        
        // Verificar acesso ao /afiliado
        $affiliateAccess = new \App\Http\Middleware\AffiliateAccess();
        $request = \Illuminate\Http\Request::create('/afiliado', 'GET');
        $request->server->set('REMOTE_ADDR', '127.0.0.1');
        $response = $affiliateAccess->handle($request, function($req) {
            return response('OK');
        });
        
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            echo "❌ Afiliado seria redirecionado de /afiliado para: " . $response->getTargetUrl() . "\n";
        } else {
            echo "✅ Afiliado pode acessar /afiliado\n";
        }
    }
    
    \Illuminate\Support\Facades\Auth::logout();
}

echo "\n==============================================\n";
echo "              RESUMO DOS ACESSOS              \n";
echo "==============================================\n";
echo "\n✅ ADMIN (lucrativa@bet.com / password):\n";
echo "   • Acesso permitido: /admin\n";
echo "   • Redirecionado de: /afiliado → /admin\n";
echo "\n✅ AFILIADO (afiliado@teste.com / password):\n";
echo "   • Acesso permitido: /afiliado\n";
echo "   • Redirecionado de: /admin → /afiliado\n";
echo "\n==============================================\n";