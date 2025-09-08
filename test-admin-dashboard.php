<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;

echo "\n==============================================\n";
echo "     TESTE DO DASHBOARD ADMIN                \n";
echo "==============================================\n";

// Verificar admin
$admin = \App\Models\User::where('email', 'lucrativa@bet.com')->first();

if (!$admin) {
    echo "❌ Admin não encontrado\n";
    exit;
}

echo "✅ Admin encontrado: " . $admin->email . "\n";
echo "✅ Tem role admin?: " . ($admin->hasRole('admin') ? 'SIM' : 'NÃO') . "\n";

// Simular login
Auth::login($admin);

if (!Auth::check()) {
    echo "❌ Não conseguiu fazer login\n";
    exit;
}

echo "✅ Login simulado com sucesso\n";

// Verificar recursos que devem estar disponíveis
echo "\n[VERIFICANDO RECURSOS]:\n";
echo "-----------------------\n";

$resources = [
    'App\Filament\Resources\SettingResource',
    'App\Filament\Resources\BannerResource',
    'App\Filament\Resources\UserResource',
    'App\Filament\Resources\WalletResource',
    'App\Filament\Resources\DepositResource',
    'App\Filament\Resources\WithdrawalResource',
    'App\Filament\Resources\AffiliateWithdrawResource',
    'App\Filament\Resources\CategoryResource',
    'App\Filament\Resources\ProviderResource',
    'App\Filament\Resources\GameResource',
    'App\Filament\Resources\CupomResource',
    'App\Filament\Resources\PromotionResource',
    'App\Filament\Resources\MissionResource',
    'App\Filament\Resources\VipResource',
    'App\Filament\Resources\DistributionSystemResource',
    'App\Filament\Resources\DailyBonusConfigResource',
    'App\Filament\Resources\GameOpenConfigResource',
];

$missing = [];
foreach ($resources as $resource) {
    if (class_exists($resource)) {
        echo "✅ " . basename(str_replace('\\', '/', $resource)) . "\n";
    } else {
        echo "❌ " . basename(str_replace('\\', '/', $resource)) . " - NÃO ENCONTRADO\n";
        $missing[] = $resource;
    }
}

// Verificar páginas
echo "\n[VERIFICANDO PÁGINAS]:\n";
echo "---------------------\n";

$pages = [
    'App\Filament\Pages\DashboardAdmin',
    'App\Filament\Pages\LayoutCssCustom',
    'App\Filament\Pages\GatewayPage',
    'App\Filament\Pages\GamesKeyPage',
    'App\Filament\Pages\AureoLinkGatewayPage',
    'App\Filament\Pages\SettingMailPage',
    'App\Filament\Pages\AffiliateHistory',
    'App\Filament\Pages\AffiliateReports',
    'App\Filament\Pages\AffiliateAnalytics',
];

foreach ($pages as $page) {
    if (class_exists($page)) {
        echo "✅ " . basename(str_replace('\\', '/', $page)) . "\n";
    } else {
        echo "❌ " . basename(str_replace('\\', '/', $page)) . " - NÃO ENCONTRADO\n";
        $missing[] = $page;
    }
}

if (!empty($missing)) {
    echo "\n⚠️  PROBLEMA ENCONTRADO!\n";
    echo "-------------------------\n";
    echo "Alguns recursos/páginas não existem:\n";
    foreach ($missing as $m) {
        echo "• " . $m . "\n";
    }
} else {
    echo "\n✅ TODOS OS RECURSOS E PÁGINAS EXISTEM!\n";
}

Auth::logout();

echo "\n==============================================\n";