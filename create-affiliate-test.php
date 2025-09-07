<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Wallet;

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

DB::beginTransaction();

try {
    // Criar usuário afiliado de teste
    $affiliate = User::create([
        'name' => 'João Afiliado',
        'email' => 'afiliado@teste.com',
        'password' => Hash::make('afiliado123'),
        'cpf' => '12345678901',
        'phone' => '11999999999',
        'inviter_code' => 'AFILIADO2025',
        'affiliate_revenue_share' => 25,
        'affiliate_cpa' => 100,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    // Criar carteira para o afiliado
    Wallet::create([
        'user_id' => $affiliate->id,
        'balance' => 0,
        'refer_rewards' => 0,
        'active' => 1
    ]);
    
    // Dar role de afiliado
    DB::table('model_has_roles')->insert([
        'role_id' => 2, // Role de afiliado
        'model_type' => 'App\Models\User',
        'model_id' => $affiliate->id
    ]);
    
    // Criar alguns usuários indicados por este afiliado
    for ($i = 1; $i <= 3; $i++) {
        $referred = User::create([
            'name' => "Cliente $i",
            'email' => "cliente$i@teste.com",
            'password' => Hash::make('cliente123'),
            'cpf' => "9876543210$i",
            'phone' => "1198888888$i",
            'inviter' => $affiliate->id,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        Wallet::create([
            'user_id' => $referred->id,
            'balance' => rand(100, 500),
            'active' => 1
        ]);
    }
    
    DB::commit();
    
    echo "✅ Afiliado de teste criado com sucesso!\n";
    echo "Email: afiliado@teste.com\n";
    echo "Senha: afiliado123\n";
    echo "Código de afiliado: AFILIADO2025\n";
    echo "3 clientes indicados criados\n";
    
} catch (Exception $e) {
    DB::rollBack();
    echo "❌ Erro ao criar afiliado: " . $e->getMessage() . "\n";
}