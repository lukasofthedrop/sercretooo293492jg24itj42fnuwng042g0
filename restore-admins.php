<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "\n🔧 RESTAURANDO USUÁRIOS ADMINISTRATIVOS\n";
echo "========================================\n\n";

// Verificar usuários existentes
$existingUsers = DB::table('users')->whereIn('email', [
    'admin@admin.com',
    'admin@lucrativabet.com', 
    'dev@lucrativabet.com'
])->pluck('email')->toArray();

echo "Usuários existentes: " . count($existingUsers) . "\n";
if (count($existingUsers) > 0) {
    echo "- " . implode("\n- ", $existingUsers) . "\n\n";
}

// Dados dos admins
$admins = [
    [
        'name' => 'Admin',
        'email' => 'admin@admin.com',
        'password' => 'admin', // Senha padrão
    ],
    [
        'name' => 'Admin Lucrativa',
        'email' => 'admin@lucrativabet.com',
        'password' => 'admin123', // Senha padrão
    ],
    [
        'name' => 'Dev Lucrativa',
        'email' => 'dev@lucrativabet.com',
        'password' => 'dev123', // Senha padrão
    ]
];

foreach ($admins as $adminData) {
    // Verificar se o usuário já existe
    $user = User::where('email', $adminData['email'])->first();
    
    if ($user) {
        echo "✓ Usuário {$adminData['email']} já existe. Atualizando senha...\n";
        // Atualizar a senha
        $user->password = Hash::make($adminData['password']);
        $user->save();
    } else {
        echo "✓ Criando usuário {$adminData['email']}...\n";
        // Criar novo usuário
        $user = User::create([
            'name' => $adminData['name'],
            'email' => $adminData['email'],
            'password' => Hash::make($adminData['password']),
            'email_verified_at' => now(),
        ]);
    }
    
    // Garantir que o usuário tenha uma carteira
    $wallet = DB::table('wallets')->where('user_id', $user->id)->first();
    if (!$wallet) {
        DB::table('wallets')->insert([
            'user_id' => $user->id,
            'balance' => 0,
            'balance_bonus' => 0,
            'balance_withdrawal' => 0,
            'refer_rewards' => 0,
            'total_bet' => 0,
            'total_won' => 0,
            'total_lose' => 0,
            'last_won' => 0,
            'last_lose' => 0,
            'currency' => 'BRL',
            'vip_level' => 0,
            'vip_points' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "  ✓ Carteira criada para {$adminData['email']}\n";
    }
}

echo "\n✅ USUÁRIOS ADMINISTRATIVOS RESTAURADOS!\n";
echo "=========================================\n";
echo "\n📝 CREDENCIAIS DE ACESSO:\n";
echo "-------------------------\n";
echo "Email: admin@admin.com\n";
echo "Senha: admin\n";
echo "\n";
echo "Email: admin@lucrativabet.com\n";
echo "Senha: admin123\n";
echo "\n";
echo "Email: dev@lucrativabet.com\n";
echo "Senha: dev123\n";
echo "\n";
echo "✅ Agora você pode fazer login no sistema!\n\n";