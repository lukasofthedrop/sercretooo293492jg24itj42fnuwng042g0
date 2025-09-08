<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

// Autenticar como afiliado de teste
$affiliate = \App\Models\User::where('email', 'afiliado@teste.com')->first();

if (!$affiliate) {
    echo "Criando afiliado de teste...\n";
    $affiliate = \App\Models\User::create([
        'name' => 'Afiliado Teste',
        'email' => 'afiliado@teste.com',
        'password' => bcrypt('password'),
        'inviter_code' => 'AFF' . time(),
        'email_verified_at' => now(),
    ]);
    echo "Afiliado criado com inviter_code: " . $affiliate->inviter_code . "\n";
}

echo "\n=== TESTE DE ACESSO AFILIADO ===\n";
echo "Email: " . $affiliate->email . "\n";
echo "Inviter Code: " . $affiliate->inviter_code . "\n";
echo "É Admin?: " . ($affiliate->hasRole('admin') ? 'SIM' : 'NÃO') . "\n";

// Simular login
\Illuminate\Support\Facades\Auth::login($affiliate);

echo "\n=== TESTE DE REDIRECIONAMENTO ===\n";

// Testar acesso ao /admin (deve redirecionar para /afiliado)
echo "Tentando acessar /admin...\n";
$adminAccess = new \App\Http\Middleware\AdminAccess();
$testRequest = Illuminate\Http\Request::create('/admin', 'GET');
$testRequest->server->set('REMOTE_ADDR', '127.0.0.1');
$response = $adminAccess->handle($testRequest, function($req) {
    return response('Acesso permitido ao admin');
});

if ($response instanceof \Illuminate\Http\RedirectResponse) {
    echo "✓ Redirecionado para: " . $response->getTargetUrl() . "\n";
} else {
    echo "✗ Não redirecionado - Resposta: " . $response->getContent() . "\n";
}

// Testar acesso ao /afiliado (deve permitir)
echo "\nTentando acessar /afiliado...\n";
$affiliateAccess = new \App\Http\Middleware\AffiliateAccess();
$testRequest = Illuminate\Http\Request::create('/afiliado', 'GET');
$testRequest->server->set('REMOTE_ADDR', '127.0.0.1');
$response = $affiliateAccess->handle($testRequest, function($req) {
    return response('Acesso permitido ao afiliado');
});

if ($response instanceof \Illuminate\Http\RedirectResponse) {
    echo "✗ Redirecionado para: " . $response->getTargetUrl() . "\n";
} else {
    echo "✓ Acesso permitido - Resposta: " . $response->getContent() . "\n";
}

// Logout
\Illuminate\Support\Facades\Auth::logout();

// Testar com admin
echo "\n=== TESTE COM ADMIN ===\n";
$admin = \App\Models\User::where('email', 'lucrativa@bet.com')->first();
if ($admin) {
    \Illuminate\Support\Facades\Auth::login($admin);
    
    echo "Email: " . $admin->email . "\n";
    echo "É Admin?: " . ($admin->hasRole('admin') ? 'SIM' : 'NÃO') . "\n";
    
    // Testar acesso ao /admin (deve permitir)
    echo "\nTentando acessar /admin...\n";
    $testRequest = Illuminate\Http\Request::create('/admin', 'GET');
    $testRequest->server->set('REMOTE_ADDR', '127.0.0.1');
    $response = $adminAccess->handle($testRequest, function($req) {
        return response('Acesso permitido ao admin');
    });
    
    if ($response instanceof \Illuminate\Http\RedirectResponse) {
        echo "✗ Redirecionado para: " . $response->getTargetUrl() . "\n";
    } else {
        echo "✓ Acesso permitido - Resposta: " . $response->getContent() . "\n";
    }
    
    // Testar acesso ao /afiliado (deve redirecionar para /admin)
    echo "\nTentando acessar /afiliado...\n";
    $testRequest = Illuminate\Http\Request::create('/afiliado', 'GET');
    $testRequest->server->set('REMOTE_ADDR', '127.0.0.1');
    $response = $affiliateAccess->handle($testRequest, function($req) {
        return response('Acesso permitido ao afiliado');
    });
    
    if ($response instanceof \Illuminate\Http\RedirectResponse) {
        echo "✓ Redirecionado para: " . $response->getTargetUrl() . "\n";
    } else {
        echo "✗ Não redirecionado - Resposta: " . $response->getContent() . "\n";
    }
}

echo "\n=== TESTE CONCLUÍDO ===\n";