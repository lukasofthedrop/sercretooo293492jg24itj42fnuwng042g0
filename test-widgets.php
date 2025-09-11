<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\ApexChartsWidget;
use App\Filament\Widgets\Top5GamesCircularWidget;
use App\Filament\Widgets\UsersRankingColumnWidget;
use App\Livewire\WalletOverview;

// Login como admin
$admin = User::where('email', 'admin@admin.com')->first();
auth()->login($admin);

echo "=== TESTE DE WIDGETS DO DASHBOARD ===\n\n";

// Lista de widgets para testar
$widgets = [
    'StatsOverview' => StatsOverview::class,
    'WalletOverview' => WalletOverview::class,
    'ApexChartsWidget' => ApexChartsWidget::class,
    'Top5GamesCircularWidget' => Top5GamesCircularWidget::class,
    'UsersRankingColumnWidget' => UsersRankingColumnWidget::class,
];

foreach ($widgets as $name => $class) {
    echo "Widget: $name\n";
    echo "Classe: $class\n";
    
    // Verificar se pode visualizar
    try {
        $canView = $class::canView();
        echo "Pode visualizar: " . ($canView ? 'SIM' : 'NÃO') . "\n";
        
        // Verificar se tem view definida
        if (property_exists($class, 'view')) {
            $reflection = new ReflectionProperty($class, 'view');
            $reflection->setAccessible(true);
            $view = $reflection->getValue();
            echo "View: $view\n";
            
            // Verificar se o arquivo de view existe
            $viewPath = resource_path('views/' . str_replace('.', '/', $view) . '.blade.php');
            echo "Arquivo view existe: " . (file_exists($viewPath) ? 'SIM' : 'NÃO') . "\n";
        } else {
            echo "View: Usa template padrão do Filament\n";
        }
    } catch (Exception $e) {
        echo "ERRO: " . $e->getMessage() . "\n";
    }
    
    echo "---\n\n";
}

// Verificar diretório de views
echo "=== ARQUIVOS DE VIEW EXISTENTES ===\n";
$viewsDir = resource_path('views/filament/widgets');
if (is_dir($viewsDir)) {
    $files = scandir($viewsDir);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "- $file\n";
        }
    }
}