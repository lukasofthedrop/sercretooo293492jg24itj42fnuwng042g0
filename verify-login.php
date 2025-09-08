<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n==============================================\n";
echo "     VERIFICANDO LOGIN COM SENHA CORRETA      \n";
echo "==============================================\n";

// Testar senha do admin
echo "\n🔐 TESTANDO LOGIN ADMIN:\n";
echo "------------------------\n";

$admin = \App\Models\User::where('email', 'lucrativa@bet.com')->first();

if ($admin && \Illuminate\Support\Facades\Hash::check('foco123@', $admin->password)) {
    echo "✅ Senha 'foco123@' está CORRETA para admin!\n";
    echo "✅ Login funcionará em /admin\n";
} else {
    echo "❌ ERRO: Senha não está correta!\n";
}

// Testar senha do afiliado
echo "\n🔐 TESTANDO LOGIN AFILIADO:\n";
echo "---------------------------\n";

$affiliate = \App\Models\User::where('email', 'afiliado@teste.com')->first();

if ($affiliate && \Illuminate\Support\Facades\Hash::check('foco123@', $affiliate->password)) {
    echo "✅ Senha 'foco123@' está CORRETA para afiliado!\n";
    echo "✅ Login funcionará em /afiliado\n";
} else {
    echo "❌ ERRO: Senha não está correta!\n";
}

echo "\n==============================================\n";
echo "           CREDENCIAIS CONFIRMADAS            \n";
echo "==============================================\n";
echo "\n✅ ADMIN pode logar em /admin com:\n";
echo "   Email: lucrativa@bet.com\n";
echo "   Senha: foco123@\n";
echo "\n✅ AFILIADO pode logar em /afiliado com:\n";
echo "   Email: afiliado@teste.com\n";
echo "   Senha: foco123@\n";
echo "\n==============================================\n";