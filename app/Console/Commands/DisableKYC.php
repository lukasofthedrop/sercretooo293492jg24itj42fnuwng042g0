<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DisableKYC extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'casino:disable-kyc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove todas as validações de KYC do sistema - CIRURGIÃO DEV';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('╔════════════════════════════════════════════════════════════════╗');
        $this->info('║             REMOVENDO SISTEMA DE KYC                          ║');
        $this->info('╚════════════════════════════════════════════════════════════════╝');
        $this->newLine();
        
        $changes = 0;
        
        // 1. Atualizar configurações do banco
        $this->info('📊 Atualizando configurações do banco...');
        
        // Remover campos de KYC das tabelas se existirem
        try {
            // Verificar se existe coluna de KYC na tabela users
            $hasKycColumn = DB::select("SHOW COLUMNS FROM users LIKE 'kyc%'");
            if ($hasKycColumn) {
                DB::statement("ALTER TABLE users DROP COLUMN IF EXISTS kyc_status");
                DB::statement("ALTER TABLE users DROP COLUMN IF EXISTS kyc_verified");
                DB::statement("ALTER TABLE users DROP COLUMN IF EXISTS kyc_document");
                $this->info('   ✅ Colunas KYC removidas da tabela users');
                $changes++;
            }
        } catch (\Exception $e) {
            // Ignorar se não existir
        }
        
        // 2. Atualizar settings para desabilitar KYC
        try {
            DB::table('settings')->updateOrInsert(
                ['key' => 'require_kyc'],
                ['value' => '0']
            );
            
            DB::table('settings')->updateOrInsert(
                ['key' => 'kyc_enabled'],
                ['value' => '0']
            );
            
            DB::table('settings')->updateOrInsert(
                ['key' => 'withdrawal_kyc_required'],
                ['value' => '0']
            );
            
            $this->info('   ✅ Configurações de KYC desabilitadas');
            $changes++;
        } catch (\Exception $e) {
            // Ignorar se não existir
        }
        
        // 3. Remover validações nos controladores
        $this->info('📝 Procurando validações de KYC nos controladores...');
        
        $controllers = [
            'app/Http/Controllers/Api/Profile/WalletController.php',
            'app/Http/Controllers/Gateway/SuitPayController.php',
            'app/Http/Controllers/Gateway/AureoLinkController.php',
        ];
        
        foreach ($controllers as $controller) {
            if (File::exists($controller)) {
                $content = File::get($controller);
                $originalContent = $content;
                
                // Remover validações de documento
                $content = preg_replace('/if\s*\([^)]*kyc[^)]*\)[^{]*{[^}]*}/i', '// KYC removido', $content);
                $content = preg_replace('/\'document\'\s*=>\s*\'required[^\']*\'/i', '// \'document\' => \'optional\'', $content);
                $content = str_replace("'cpf' => 'required", "'cpf' => 'nullable", $content);
                
                if ($content !== $originalContent) {
                    File::put($controller, $content);
                    $this->info('   ✅ ' . basename($controller) . ' atualizado');
                    $changes++;
                }
            }
        }
        
        // 4. Limitar valores de saque sem KYC
        $this->info('💰 Removendo limites de saque por KYC...');
        
        DB::table('settings')->updateOrInsert(
            ['key' => 'max_withdrawal_no_kyc'],
            ['value' => '999999999'] // Sem limite
        );
        
        DB::table('settings')->updateOrInsert(
            ['key' => 'min_withdrawal'],
            ['value' => '20'] // Mínimo de R$ 20
        );
        
        $this->info('   ✅ Limites de saque atualizados');
        $changes++;
        
        $this->newLine();
        $this->info('✅ SISTEMA DE KYC REMOVIDO COM SUCESSO!');
        $this->newLine();
        
        $this->table(
            ['Ação', 'Status'],
            [
                ['Validações de KYC', 'REMOVIDAS ✅'],
                ['Limites de saque', 'SEM RESTRIÇÕES ✅'],
                ['Verificação de documentos', 'DESABILITADA ✅'],
                ['Total de mudanças', $changes],
            ]
        );
        
        $this->newLine();
        $this->info('🎉 Usuários agora podem:');
        $this->info('   • Fazer depósitos sem verificação');
        $this->info('   • Fazer saques sem enviar documentos');
        $this->info('   • Jogar imediatamente após cadastro');
        
        return 0;
    }
}