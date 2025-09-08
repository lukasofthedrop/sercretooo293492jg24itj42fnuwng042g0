<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n==============================================\n";
echo "     TESTE DE URLs DE IMAGENS DOS BANNERS    \n";
echo "==============================================\n";

$banners = \App\Models\Banner::all();

echo "\n[ANÁLISE DAS URLs]:\n";
echo "-------------------\n";

foreach ($banners as $banner) {
    echo "\nBanner ID: " . $banner->id . "\n";
    echo "Valor no banco: " . $banner->image . "\n";
    
    // Testar diferentes formatos de URL
    $urls = [
        'Original' => $banner->image,
        'Com /storage/' => '/storage/' . $banner->image,
        'Com storage/' => 'storage/' . $banner->image,
        'Asset storage' => asset('storage/' . $banner->image),
        'Storage URL' => \Storage::disk('public')->url($banner->image),
    ];
    
    foreach ($urls as $tipo => $url) {
        echo "  {$tipo}: {$url}\n";
        
        // Verificar se arquivo existe fisicamente
        if (strpos($url, 'http') === false) {
            $path = public_path($url);
            if (file_exists($path)) {
                echo "    ✅ Arquivo existe em: {$path}\n";
            }
        }
    }
    
    echo "---\n";
}

// Verificar configuração do storage
echo "\n[CONFIGURAÇÃO DO STORAGE]:\n";
echo "-------------------------\n";
echo "Storage path: " . storage_path('app/public') . "\n";
echo "Public path: " . public_path('storage') . "\n";

// Verificar se o link simbólico existe
if (is_link(public_path('storage'))) {
    echo "✅ Link simbólico existe\n";
    $target = readlink(public_path('storage'));
    echo "   Aponta para: " . $target . "\n";
} else {
    echo "❌ Link simbólico NÃO existe!\n";
}

echo "\n==============================================\n";