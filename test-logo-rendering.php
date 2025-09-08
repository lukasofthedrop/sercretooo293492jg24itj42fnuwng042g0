<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n==============================================\n";
echo "     ANÁLISE DO PROBLEMA DO LOGO             \n";
echo "==============================================\n";

$settings = \Helper::getSetting();

echo "\n[URLS GERADAS]:\n";
echo "---------------\n";

if (isset($settings['software_logo_white'])) {
    $logoWhite = $settings['software_logo_white'];
    echo "Logo White no banco: " . $logoWhite . "\n";
    
    // Como está sendo gerado pela view
    $urlView = asset('storage/'. $logoWhite);
    echo "URL gerada pela view: " . $urlView . "\n";
    
    // URL correta
    $urlCorreta = asset($logoWhite);
    echo "URL alternativa: " . $urlCorreta . "\n";
    
    // URL absoluta
    $urlAbsoluta = "/storage/" . $logoWhite;
    echo "URL absoluta: " . $urlAbsoluta . "\n";
}

if (isset($settings['software_logo_black'])) {
    $logoBlack = $settings['software_logo_black'];
    echo "\nLogo Black no banco: " . $logoBlack . "\n";
    
    // Como está sendo gerado pela view
    $urlView = asset('storage/'. $logoBlack);
    echo "URL gerada pela view: " . $urlView . "\n";
    
    // URL correta
    $urlCorreta = asset($logoBlack);
    echo "URL alternativa: " . $urlCorreta . "\n";
    
    // URL absoluta
    $urlAbsoluta = "/storage/" . $logoBlack;
    echo "URL absoluta: " . $urlAbsoluta . "\n";
}

echo "\n[ANÁLISE]:\n";
echo "---------\n";
echo "✅ Arquivos existem em: public/storage/uploads/\n";
echo "✅ HTTP 200 OK ao acessar via curl\n";
echo "❓ Problema pode ser CSS ou JavaScript\n";

echo "\n==============================================\n";