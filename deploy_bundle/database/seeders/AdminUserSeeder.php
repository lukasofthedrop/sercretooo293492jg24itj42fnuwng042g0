<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar o role de admin se não existir
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $affiliateRole = Role::firstOrCreate(['name' => 'afiliado']);
        
        $admin = User::firstOrCreate(
            ['email' => 'lucrativa@bet.com'],
            [
                'name' => 'Lucrativa Admin',
                'password' => Hash::make('foco123@'),
                'email_verified_at' => now(),
                'inviter_code' => 'ADMIN2025',
            ]
        );
        $admin->assignRole($adminRole);
        $admin->update([
            'two_factor_secret' => 'MEFWMGSAV4Y4BCR6',
            'two_factor_confirmed_at' => now(),
        ]);

        $affiliate = User::firstOrCreate(
            ['email' => 'afiliado@lucrativabet.com'],
            [
                'name' => 'Afiliado Demo',
                'password' => Hash::make('afiliado123'),
                'inviter_code' => 'AFF-DEMO-001',
                'email_verified_at' => now(),
            ]
        );
        $affiliate->assignRole($affiliateRole);

        DB::table('wallets')->updateOrInsert(
            ['user_id' => $affiliate->id],
            [
                'balance' => 1250,
                'balance_bonus' => 150,
                'balance_withdrawal' => 0,
                'refer_rewards' => 420,
                'total_bet' => 3120,
                'total_won' => 1890,
                'total_lose' => 950,
                'last_won' => 180,
                'last_lose' => 90,
                'currency' => 'BRL',
                'symbol' => 'R$',
                'vip_level' => 2,
                'vip_points' => 480,
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command?->info('Usuários demo criados: admin@admin.com / admin123 // afiliado@lucrativabet.com / afiliado123');
    }
}
