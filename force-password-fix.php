<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

echo "\n==============================================\n";
echo "   FORÇANDO CORREÇÃO DE SENHAS - foco123@     \n";
echo "==============================================\n";

// ADMIN
$admin = \App\Models\User::where('email', 'lucrativa@bet.com')->first();
if ($admin) {
    // Forçar atualização direta
    DB::table('users')
        ->where('id', $admin->id)
        ->update(['password' => Hash::make('foco123@')]);
    
    echo "✅ Admin senha forçada para: foco123@\n";
    
    // Verificar
    $admin->refresh();
    if (Hash::check('foco123@', $admin->password)) {
        echo "✅ CONFIRMADO: Login admin funcionando!\n";
    } else {
        echo "⚠️  Verificação falhou, tentando novamente...\n";
        $admin->password = Hash::make('foco123@');
        $admin->save();
        echo "✅ Segunda tentativa realizada\n";
    }
}

// AFILIADO
$affiliate = \App\Models\User::where('email', 'afiliado@teste.com')->first();
if ($affiliate) {
    // Forçar atualização direta
    DB::table('users')
        ->where('id', $affiliate->id)
        ->update(['password' => Hash::make('foco123@')]);
    
    echo "✅ Afiliado senha forçada para: foco123@\n";
    
    // Verificar
    $affiliate->refresh();
    if (Hash::check('foco123@', $affiliate->password)) {
        echo "✅ CONFIRMADO: Login afiliado funcionando!\n";
    } else {
        echo "⚠️  Verificação falhou, tentando novamente...\n";
        $affiliate->password = Hash::make('foco123@');
        $affiliate->save();
        echo "✅ Segunda tentativa realizada\n";
    }
}

echo "\n==============================================\n";
echo "             SENHAS CORRIGIDAS                \n";
echo "==============================================\n";
echo "\n🔑 CREDENCIAIS PARA LOGIN:\n";
echo "----------------------------\n";
echo "\nADMIN (/admin):\n";
echo "  Email: lucrativa@bet.com\n";
echo "  Senha: foco123@\n";
echo "\nAFILIADO (/afiliado):\n";
echo "  Email: afiliado@teste.com\n";
echo "  Senha: foco123@\n";
echo "\n==============================================\n";
echo "✅ PRONTO! Pode fazer login agora!\n";
echo "==============================================\n";