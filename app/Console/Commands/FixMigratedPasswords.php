<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class FixMigratedPasswords extends Command
{
    protected $signature = 'users:fix-passwords {--test=0 : Test with N users}';
    protected $description = 'Fix double-hashed passwords from migration';

    public function handle()
    {
        $testLimit = $this->option('test');
        
        $this->info('╔════════════════════════════════════════════════╗');
        $this->info('║     CORREÇÃO DE SENHAS - MIGRAÇÃO              ║');
        $this->info('╚════════════════════════════════════════════════╝');
        $this->info('');
        
        // Get users with inviter_code (migrated users)
        $query = DB::table('users')
            ->whereNotNull('inviter_code')
            ->where('inviter_code', '!=', '');
            
        if ($testLimit > 0) {
            $query->limit($testLimit);
        }
        
        $users = $query->get(['id', 'email']);
        $total = $users->count();
        
        $this->info("🔧 Corrigindo senhas de {$total} usuários...");
        $this->info('');
        
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        
        $fixedCount = 0;
        $correctHash = Hash::make('trocar@123');
        
        foreach ($users as $user) {
            // Update directly in database to bypass mutator
            DB::table('users')
                ->where('id', $user->id)
                ->update(['password' => $correctHash]);
                
            $fixedCount++;
            $bar->advance();
        }
        
        $bar->finish();
        $this->info('');
        $this->info('');
        $this->info('✅ Correção concluída!');
        $this->info("   • {$fixedCount} senhas corrigidas");
        $this->info('   • Senha padrão: trocar@123');
        $this->info('');
        
        return 0;
    }
}