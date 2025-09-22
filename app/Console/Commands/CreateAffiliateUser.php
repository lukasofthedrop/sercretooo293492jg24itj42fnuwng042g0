<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateAffiliateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-affiliate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create affiliate test user safely in production';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating affiliate test users safely...');

        // Skip delete in production
        if (app()->environment() === 'production') {
            $this->info('Production mode - skipping delete');
        } else {
            User::whereIn('email', ['lucrativa@bet.com', 'afiliado@lucrativa.bet', 'jogador@lucrativa.bet'])->delete();
            $this->info('Local users deleted');
        }

        $admin = User::firstOrCreate(
            ['email' => 'lucrativa@bet.com'],
            [
                'name' => 'Admin Lucrativa',
                'password' => Hash::make('foco123@'),
                'email_verified_at' => now(),
                'user_type' => 'admin',
                'status' => 1,
            ]
        );
        $this->info('Admin: ' . $admin->id);

        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $admin->assignRole($adminRole);
        $this->info('Admin role assigned');

        $affiliate = User::firstOrCreate(
            ['email' => 'afiliado@lucrativa.bet'],
            [
                'name' => 'Afiliado Teste',
                'password' => Hash::make('afiliado123'),
                'email_verified_at' => now(),
                'user_type' => 'user',
                'status' => 1,
            ]
        );
        $this->info('Afiliado: ' . $affiliate->id);

        $affiliateRole = Role::firstOrCreate(['name' => 'Affiliate']);
        $affiliate->assignRole($affiliateRole);
        $this->info('Affiliate role assigned');

        $player = User::firstOrCreate(
            ['email' => 'jogador@lucrativa.bet'],
            [
                'name' => 'Jogador Teste',
                'password' => Hash::make('jogador123'),
                'inviter' => $affiliate->id,
                'email_verified_at' => now(),
                'user_type' => 'user',
                'status' => 1,
            ]
        );
        $this->info('Player: ' . $player->id);

        $playerRole = Role::firstOrCreate(['name' => 'Player']);
        $player->assignRole($playerRole);
        $this->info('Player role assigned');

        $this->warn('Usuarios criados! Login: afiliado@lucrativa.bet / afiliado123');
        $this->warn('Teste em: https://lucrativabet-web-production.up.railway.app/afiliado/login');
    }
}
