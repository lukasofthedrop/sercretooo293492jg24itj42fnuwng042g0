<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n==============================================\n";
echo "     TESTE DE LOGOS DO ADMIN                 \n";
echo "==============================================\n";

// Verificar o que o Helper retorna
$settings = \Helper::getSetting();

echo "\n[CONFIGURAÇÕES DO HELPER]:\n";
echo "--------------------------\n";

if (empty($settings)) {
    echo "❌ Settings está vazio!\n";
} else {
    echo "✅ Settings encontrado\n";
    
    // Verificar logos
    if (isset($settings['software_logo_white'])) {
        echo "\nLogo White: " . $settings['software_logo_white'] . "\n";
        
        // Verificar se arquivo existe
        $paths = [
            'storage/' => public_path('storage/' . $settings['software_logo_white']),
            'storage/uploads/' => public_path('storage/uploads/' . $settings['software_logo_white']),
            'direct' => public_path($settings['software_logo_white']),
        ];
        
        foreach ($paths as $type => $path) {
            if (file_exists($path)) {
                echo "  ✅ Existe em: {$type} => {$path}\n";
            } else {
                echo "  ❌ NÃO existe em: {$type}\n";
            }
        }
    } else {
        echo "❌ software_logo_white não definido\n";
    }
    
    if (isset($settings['software_logo_black'])) {
        echo "\nLogo Black: " . $settings['software_logo_black'] . "\n";
        
        // Verificar se arquivo existe
        $paths = [
            'storage/' => public_path('storage/' . $settings['software_logo_black']),
            'storage/uploads/' => public_path('storage/uploads/' . $settings['software_logo_black']),
            'direct' => public_path($settings['software_logo_black']),
        ];
        
        foreach ($paths as $type => $path) {
            if (file_exists($path)) {
                echo "  ✅ Existe em: {$type} => {$path}\n";
            } else {
                echo "  ❌ NÃO existe em: {$type}\n";
            }
        }
    } else {
        echo "❌ software_logo_black não definido\n";
    }
}

// Verificar tabela settings diretamente
echo "\n[TABELA SETTINGS]:\n";
echo "------------------\n";
$setting = \App\Models\Setting::first();
if ($setting) {
    echo "ID: " . $setting->id . "\n";
    echo "Logo White: " . ($setting->software_logo_white ?? 'NULL') . "\n";
    echo "Logo Black: " . ($setting->software_logo_black ?? 'NULL') . "\n";
} else {
    echo "❌ Nenhum registro na tabela settings\n";
}

echo "\n==============================================\n";