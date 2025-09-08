<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n==============================================\n";
echo "   CORRIGINDO SENHAS - VOLTANDO AO ORIGINAL   \n";
echo "==============================================\n";

// CORRIGIR ADMIN
echo "\n🔧 CORRIGINDO SENHA DO ADMIN:\n";
echo "-----------------------------\n";

$admin = \App\Models\User::where('email', 'lucrativa@bet.com')->first();

if ($admin) {
    $admin->password = bcrypt('foco123@');
    $admin->save();
    echo "✅ Senha do ADMIN restaurada para: foco123@\n";
    echo "✅ Email: lucrativa@bet.com\n";
} else {
    echo "❌ Admin não encontrado! Criando com senha correta...\n";
    $admin = \App\Models\User::create([
        'name' => 'Admin LucrativaBet',
        'email' => 'lucrativa@bet.com',
        'password' => bcrypt('foco123@'),
        'email_verified_at' => now(),
    ]);
    $admin->assignRole('admin');
    echo "✅ Admin criado com senha: foco123@\n";
}

// CORRIGIR AFILIADO
echo "\n🔧 CORRIGINDO SENHA DO AFILIADO:\n";
echo "---------------------------------\n";

$affiliate = \App\Models\User::where('email', 'afiliado@teste.com')->first();

if ($affiliate) {
    $affiliate->password = bcrypt('foco123@');
    $affiliate->save();
    echo "✅ Senha do AFILIADO restaurada para: foco123@\n";
    echo "✅ Email: afiliado@teste.com\n";
} else {
    echo "❌ Afiliado não encontrado! Criando com senha correta...\n";
    $affiliate = \App\Models\User::create([
        'name' => 'Afiliado Teste',
        'email' => 'afiliado@teste.com',
        'password' => bcrypt('foco123@'),
        'inviter_code' => 'AFF' . time(),
        'email_verified_at' => now(),
    ]);
    echo "✅ Afiliado criado com senha: foco123@\n";
}

echo "\n==============================================\n";
echo "         SENHAS CORRIGIDAS COM SUCESSO        \n";
echo "==============================================\n";
echo "\n🔐 ADMIN (/admin):\n";
echo "   Email: lucrativa@bet.com\n";
echo "   Senha: foco123@\n";
echo "\n🔐 AFILIADO (/afiliado):\n";
echo "   Email: afiliado@teste.com\n";
echo "   Senha: foco123@\n";
echo "\n==============================================\n";
echo "✅ SENHAS RESTAURADAS PARA O ORIGINAL!\n";
echo "==============================================\n\n";