<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n==============================================\n";
echo "     TESTE DE BANNERS - VERIFICAÇÃO          \n";
echo "==============================================\n";

// Verificar banners no banco
$banners = \App\Models\Banner::all();

echo "\n[BANNERS NO BANCO DE DADOS]:\n";
echo "----------------------------\n";
echo "Total de banners: " . $banners->count() . "\n\n";

foreach ($banners as $banner) {
    echo "ID: " . $banner->id . "\n";
    echo "Imagem no banco: " . $banner->image . "\n";
    
    // Verificar se o arquivo existe
    $fullPath = public_path($banner->image);
    $storageUploadPath = public_path('storage/' . $banner->image);
    $uploadsPath = public_path('storage/uploads/' . basename($banner->image));
    
    echo "Testando caminhos:\n";
    echo "  1. " . $fullPath . " => " . (file_exists($fullPath) ? "✅ EXISTE" : "❌ NÃO EXISTE") . "\n";
    echo "  2. " . $storageUploadPath . " => " . (file_exists($storageUploadPath) ? "✅ EXISTE" : "❌ NÃO EXISTE") . "\n";
    echo "  3. " . $uploadsPath . " => " . (file_exists($uploadsPath) ? "✅ EXISTE" : "❌ NÃO EXISTE") . "\n";
    
    echo "---\n";
}

echo "\n==============================================\n";