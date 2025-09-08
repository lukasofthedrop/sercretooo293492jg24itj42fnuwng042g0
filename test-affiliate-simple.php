<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Buscar ou criar afiliado de teste
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

// Testar com admin
echo "\n=== TESTE COM ADMIN ===\n";
$admin = \App\Models\User::where('email', 'lucrativa@bet.com')->first();
if ($admin) {
    echo "Email: " . $admin->email . "\n";
    echo "É Admin?: " . ($admin->hasRole('admin') ? 'SIM' : 'NÃO') . "\n";
    echo "Inviter Code: " . ($admin->inviter_code ?: 'Nenhum') . "\n";
}

echo "\n=== VERIFICANDO CONFIGURAÇÃO DOS PANELS ===\n";

// Verificar se os panels estão registrados
$config = config('app.providers');
$hasAdminPanel = in_array('App\Providers\Filament\AdminPanelProvider', $config);
$hasAffiliatePanel = in_array('App\Providers\Filament\AffiliatePanelProvider', $config);

echo "AdminPanelProvider registrado: " . ($hasAdminPanel ? 'SIM' : 'NÃO') . "\n";
echo "AffiliatePanelProvider registrado: " . ($hasAffiliatePanel ? 'SIM' : 'NÃO') . "\n";

echo "\n=== ROTAS DISPONÍVEIS ===\n";

// Listar rotas de afiliado
$routes = app('router')->getRoutes();
$affiliateRoutes = [];
$adminRoutes = [];

foreach ($routes as $route) {
    $uri = $route->uri();
    if (strpos($uri, 'afiliado') === 0) {
        $affiliateRoutes[] = $uri;
    }
    if (strpos($uri, 'admin') === 0) {
        $adminRoutes[] = $uri;
    }
}

echo "\nRotas de Afiliado (primeiras 5):\n";
foreach (array_slice($affiliateRoutes, 0, 5) as $route) {
    echo "  - /" . $route . "\n";
}

echo "\nRotas de Admin (primeiras 5):\n";
foreach (array_slice($adminRoutes, 0, 5) as $route) {
    echo "  - /" . $route . "\n";
}

echo "\n=== TESTE CONCLUÍDO ===\n";