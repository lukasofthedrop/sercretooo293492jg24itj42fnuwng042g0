<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n==============================================\n";
echo "   VERIFICAÇÃO DE CREDENCIAIS - LUCRATIVABET   \n";
echo "==============================================\n";

// Verificar admin principal
echo "\n📌 ADMIN PRINCIPAL:\n";
echo "-------------------\n";

$admin = \App\Models\User::where('email', 'lucrativa@bet.com')->first();

if ($admin) {
    echo "✅ Email: lucrativa@bet.com\n";
    echo "✅ Nome: " . $admin->name . "\n";
    echo "✅ Role: " . ($admin->hasRole('admin') ? 'ADMIN' : 'USER') . "\n";
    echo "✅ ID: " . $admin->id . "\n";
    echo "✅ Inviter Code: " . ($admin->inviter_code ?: 'Nenhum') . "\n";
    
    // Resetar senha para 'password'
    $admin->password = bcrypt('password');
    $admin->save();
    echo "✅ Senha resetada para: password\n";
} else {
    echo "❌ Admin não encontrado! Criando...\n";
    $admin = \App\Models\User::create([
        'name' => 'Admin LucrativaBet',
        'email' => 'lucrativa@bet.com',
        'password' => bcrypt('password'),
        'email_verified_at' => now(),
    ]);
    
    // Atribuir role de admin
    $admin->assignRole('admin');
    echo "✅ Admin criado com sucesso!\n";
    echo "✅ Email: lucrativa@bet.com\n";
    echo "✅ Senha: password\n";
}

// Verificar afiliado de teste
echo "\n📌 AFILIADO DE TESTE:\n";
echo "---------------------\n";

$affiliate = \App\Models\User::where('email', 'afiliado@teste.com')->first();

if ($affiliate) {
    echo "✅ Email: afiliado@teste.com\n";
    echo "✅ Nome: " . $affiliate->name . "\n";
    echo "✅ Role: " . ($affiliate->hasRole('admin') ? 'ADMIN' : 'AFILIADO') . "\n";
    echo "✅ Inviter Code: " . $affiliate->inviter_code . "\n";
    
    // Resetar senha para 'password'
    $affiliate->password = bcrypt('password');
    $affiliate->save();
    echo "✅ Senha resetada para: password\n";
} else {
    echo "❌ Afiliado não encontrado! Criando...\n";
    $affiliate = \App\Models\User::create([
        'name' => 'Afiliado Teste',
        'email' => 'afiliado@teste.com',
        'password' => bcrypt('password'),
        'inviter_code' => 'AFF' . time(),
        'email_verified_at' => now(),
    ]);
    echo "✅ Afiliado criado com sucesso!\n";
    echo "✅ Email: afiliado@teste.com\n";
    echo "✅ Senha: password\n";
    echo "✅ Inviter Code: " . $affiliate->inviter_code . "\n";
}

// Garantir que o afiliado NÃO tem role de admin
if ($affiliate->hasRole('admin')) {
    $affiliate->removeRole('admin');
    echo "⚠️  Removida role admin do afiliado (correção)\n";
}

// Listar outros usuários admin
echo "\n📌 OUTROS ADMINS NO SISTEMA:\n";
echo "----------------------------\n";

$admins = \App\Models\User::whereHas('roles', function($q) {
    $q->where('name', 'admin');
})->where('email', '!=', 'lucrativa@bet.com')->get();

if ($admins->count() > 0) {
    foreach ($admins as $adm) {
        echo "• " . $adm->email . " (ID: " . $adm->id . ")\n";
    }
} else {
    echo "Nenhum outro admin encontrado.\n";
}

// Listar outros afiliados
echo "\n📌 OUTROS AFILIADOS NO SISTEMA:\n";
echo "--------------------------------\n";

$affiliates = \App\Models\User::whereNotNull('inviter_code')
    ->whereDoesntHave('roles', function($q) {
        $q->where('name', 'admin');
    })
    ->where('email', '!=', 'afiliado@teste.com')
    ->limit(5)
    ->get();

if ($affiliates->count() > 0) {
    foreach ($affiliates as $aff) {
        echo "• " . $aff->email . " (Code: " . $aff->inviter_code . ")\n";
    }
} else {
    echo "Nenhum outro afiliado encontrado.\n";
}

echo "\n==============================================\n";
echo "          CREDENCIAIS PARA ACESSO             \n";
echo "==============================================\n";
echo "\n🔐 ADMIN (/admin):\n";
echo "   Email: lucrativa@bet.com\n";
echo "   Senha: password\n";
echo "\n🔐 AFILIADO (/afiliado):\n";
echo "   Email: afiliado@teste.com\n";
echo "   Senha: password\n";
echo "\n==============================================\n";
echo "✅ Credenciais verificadas e resetadas!\n";
echo "==============================================\n\n";