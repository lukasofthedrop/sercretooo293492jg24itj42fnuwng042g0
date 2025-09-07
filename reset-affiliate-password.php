<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$affiliate = User::find(26);
if ($affiliate) {
    $affiliate->password = bcrypt('senha123');
    $affiliate->save();
    
    echo "=== SENHA RESETADA COM SUCESSO ===\n";
    echo "Email: " . $affiliate->email . "\n";
    echo "Nova senha: senha123\n";
    echo "Código afiliado: " . $affiliate->inviter_code . "\n";
    
    // Verificar se tem role de afiliado
    $hasRole = \DB::table('model_has_roles')
        ->where('model_id', $affiliate->id)
        ->where('model_type', 'App\Models\User')
        ->exists();
    
    if (!$hasRole) {
        \DB::table('model_has_roles')->insert([
            'role_id' => 2,
            'model_type' => 'App\Models\User',
            'model_id' => $affiliate->id,
        ]);
        echo "Role de afiliado adicionada.\n";
    } else {
        echo "Usuário já tem role atribuída.\n";
    }
    
    // Verificar se tem wallet
    $wallet = \DB::table('wallets')->where('user_id', $affiliate->id)->first();
    if (!$wallet) {
        \DB::table('wallets')->insert([
            'user_id' => $affiliate->id,
            'balance' => 0,
            'bonus' => 0,
            'refer_rewards' => 2500.00, // Simular ganhos
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "Wallet criada com R$ 2.500,00 em comissões.\n";
    } else {
        echo "Wallet existente - Saldo de comissões: R$ " . number_format($wallet->refer_rewards, 2, ',', '.') . "\n";
    }
    
    // Criar alguns indicados fake para teste
    echo "\n=== CRIANDO INDICADOS DE TESTE ===\n";
    
    $indicados = [
        ['name' => 'Maria Silva', 'email' => 'maria.teste@example.com'],
        ['name' => 'Pedro Santos', 'email' => 'pedro.teste@example.com'],
        ['name' => 'Ana Costa', 'email' => 'ana.teste@example.com'],
    ];
    
    foreach ($indicados as $indicado) {
        $exists = User::where('email', $indicado['email'])->exists();
        if (!$exists) {
            $user = User::create([
                'name' => $indicado['name'],
                'email' => $indicado['email'],
                'password' => bcrypt('senha123'),
                'inviter' => 26, // João Afiliado como quem convidou
            ]);
            
            // Criar wallet
            \DB::table('wallets')->insert([
                'user_id' => $user->id,
                'balance' => rand(100, 1000),
                'bonus' => 0,
                'refer_rewards' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            // Criar alguns depósitos fake
            \DB::table('deposits')->insert([
                'user_id' => $user->id,
                'amount' => rand(100, 500),
                'type' => 'pix',
                'status' => 1,
                'currency' => 'BRL',
                'created_at' => now()->subDays(rand(1, 15)),
                'updated_at' => now(),
            ]);
            
            echo "Criado indicado: {$indicado['name']}\n";
        }
    }
    
} else {
    echo "Usuário afiliado não encontrado!\n";
}