<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Skip DB connection test in remote execution to avoid internal hostname issues
        $this->command->info('Skipping DB connection test for remote execution at ' . now());

        // Delete existing test users if any
        User::whereIn('email', [
            'admin@lucrativa.bet',
            'afiliado@lucrativa.bet',
            'jogador@lucrativa.bet'
        ])->delete();

        // Create Admin with correct credentials
        $admin = User::firstOrCreate(
            ['email' => 'admin@lucrativa.bet'],
            [
                'name' => 'Admin Lucrativa',
                'password' => Hash::make('foco123@'), // Correct password
                'email_verified_at' => now(),
                'is_admin' => 1,
            ]
        );

        // Assign admin role
        Role::firstOrCreate(['name' => 'admin']);
        $admin->assignRole('admin');

        // Create Affiliate
        $affiliate = User::create([
            'name' => 'Afiliado Teste',
            'email' => 'afiliado@lucrativa.bet',
            'password' => Hash::make('afiliado123'),
            'email_verified_at' => now(),
        ]);

        // Assign affiliate role
        Role::firstOrCreate(['name' => 'afiliado']);
        $affiliate->assignRole('afiliado');

        // Create Player
        $player = User::create([
            'name' => 'Jogador Teste',
            'email' => 'jogador@lucrativa.bet',
            'password' => Hash::make('jogador123'),
            'inviter' => $affiliate->id, // Linked to affiliate
            'email_verified_at' => now(),
        ]);

        // Assign player role if needed
        Role::firstOrCreate(['name' => 'Player']);
        $player->assignRole('Player');

        $this->command->info('Test users created/verified: Admin (admin@lucrativa.bet / foco123@), Affiliate (afiliado@lucrativa.bet / afiliado123), Player (jogador@lucrativa.bet / jogador123)');
    }
}
