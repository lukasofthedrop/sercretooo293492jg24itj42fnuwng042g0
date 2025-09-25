<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar o role de admin se não existir
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        
        // Criar usuário admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'email' => 'admin@admin.com',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );
        
        // Atribuir role de admin ao usuário
        $admin->assignRole($adminRole);
        
        $this->command->info('Admin user created: admin@admin.com / admin123');
    }
}
