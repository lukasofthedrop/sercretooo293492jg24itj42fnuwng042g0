<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

// Buscar afiliado
$affiliate = User::where('email', 'afiliado@teste.com')->first();

if ($affiliate) {
    // Resetar senha
    $affiliate->password = bcrypt('senha123');
    $affiliate->save();
    
    echo "=== CONTA AFILIADO PRONTA ===\n";
    echo "Email: " . $affiliate->email . "\n";
    echo "Senha: senha123\n";
    echo "Código: " . $affiliate->inviter_code . "\n";
    
    // Verificar role
    $hasRole = \DB::table('model_has_roles')
        ->where('model_id', $affiliate->id)
        ->where('model_type', 'App\Models\User')
        ->where('role_id', 2) // Role de afiliado
        ->exists();
    
    if (!$hasRole) {
        \DB::table('model_has_roles')->insert([
            'role_id' => 2,
            'model_type' => 'App\Models\User',
            'model_id' => $affiliate->id,
        ]);
        echo "Role de afiliado adicionada.\n";
    }
    
    echo "\n=== INSTRUÇÕES ===\n";
    echo "1. Acesse: http://localhost:8080/admin/login\n";
    echo "2. Use as credenciais acima\n";
    echo "3. Você verá apenas o menu 'Minha Dashboard Afiliado'\n";
} else {
    echo "Erro: Conta afiliado não encontrada!\n";
}