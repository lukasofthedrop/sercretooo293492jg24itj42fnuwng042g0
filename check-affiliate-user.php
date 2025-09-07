<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

// Buscar usuário afiliado de teste
$affiliate = User::where('email', 'afiliado@teste.com')->first();

if ($affiliate) {
    echo "=== USUÁRIO AFILIADO ENCONTRADO ===\n";
    echo "ID: " . $affiliate->id . "\n";
    echo "Nome: " . $affiliate->name . "\n";
    echo "Email: " . $affiliate->email . "\n";
    echo "Código Afiliado: " . ($affiliate->inviter_code ?? 'SEM CÓDIGO') . "\n";
} else {
    echo "Usuário afiliado@teste.com não encontrado.\n";
    
    // Criar usuário afiliado de teste
    echo "\n=== CRIANDO USUÁRIO AFILIADO DE TESTE ===\n";
    
    $affiliate = User::create([
        'name' => 'João Afiliado Teste',
        'email' => 'afiliado@teste.com',
        'password' => bcrypt('senha123'),
        'inviter_code' => 'AFF2025TEST',
        'inviter' => 15, // Admin como quem convidou
    ]);
    
    // Criar wallet para o afiliado
    \DB::table('wallets')->insert([
        'user_id' => $affiliate->id,
        'balance' => 0,
        'bonus' => 0,
        'refer_rewards' => 1500.00, // Simular que já ganhou algo
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    // Adicionar role de afiliado
    \DB::table('model_has_roles')->insert([
        'role_id' => 2,
        'model_type' => 'App\Models\User',
        'model_id' => $affiliate->id,
    ]);
    
    echo "Usuário criado com sucesso!\n";
    echo "Email: afiliado@teste.com\n";
    echo "Senha: senha123\n";
    echo "Código: AFF2025TEST\n";
}

// Verificar outros usuários não-admin
echo "\n=== OUTROS USUÁRIOS NO SISTEMA ===\n";
$users = User::where('id', '!=', 15)->limit(5)->get(['id', 'name', 'email', 'inviter_code']);
foreach ($users as $user) {
    echo "ID: {$user->id} | Nome: {$user->name} | Email: {$user->email} | Código: " . ($user->inviter_code ?? 'N/A') . "\n";
}