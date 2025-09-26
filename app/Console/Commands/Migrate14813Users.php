<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;

class Migrate14813Users extends Command
{
    protected $signature = 'users:migrate14813 {--test=0 : Número de usuários para teste} {--dry-run : Simular sem inserir}';
    protected $description = 'Migra 14.813 usuários do CSV para o banco';

    private $successCount = 0;
    private $errorCount = 0;
    private $duplicateCount = 0;
    private $walletCount = 0;
    private $errors = [];
    private $duplicateEmails = [];
    private $totalBalance = 0;
    private $totalBonus = 0;
    private $processedEmails = [];

    public function handle()
    {
        $testLimit = $this->option('test');
        $dryRun = $this->option('dry-run');
        $file = '/Users/rkripto/Downloads/lucrativabet/users_eexport_217_1757454496.csv';

        if (!file_exists($file)) {
            $this->error("Arquivo não encontrado: $file");
            return 1;
        }

        $this->info('╔════════════════════════════════════════════════╗');
        $this->info('║     MIGRAÇÃO DE 14.813 USUÁRIOS - LUCRATIVABET    ║');
        $this->info('╚════════════════════════════════════════════════╝');
        $this->info('');
        
        $this->info('📋 Configurações:');
        $this->info('   • Senha padrão: trocar@123');
        $this->info('   • Duplicatas: Serão ignoradas');
        $this->info('   • Saldos: Serão migrados');
        $this->info('   • Arquivo: ' . basename($file));
        
        if ($dryRun) {
            $this->warn('   🔸 MODO DRY-RUN: Nenhum dado será inserido');
        }
        
        if ($testLimit > 0) {
            $this->warn("   🔸 MODO TESTE: Limitado a $testLimit usuários");
        }

        $this->info('');

        // Carregar arquivo CSV
        $users = $this->loadCSV($file);

        if (empty($users)) {
            $this->error('Nenhum usuário encontrado no arquivo');
            return 1;
        }

        $totalUsers = count($users);
        $this->info("📊 Total de usuários no arquivo: " . number_format($totalUsers, 0, ',', '.'));
        
        if ($testLimit > 0 && $testLimit < $totalUsers) {
            $users = array_slice($users, 0, $testLimit);
            $this->info("📊 Processando apenas: $testLimit usuários (teste)");
        }

        $this->info('');
        
        if (!$testLimit) {
            $confirm = $this->confirm('⚠️  Deseja REALMENTE migrar ' . number_format(count($users), 0, ',', '.') . ' usuários agora?');
            
            if (!$confirm) {
                $this->info('Migração cancelada');
                return 0;
            }
        }

        $this->info('');
        $this->info('🚀 Iniciando migração...');
        $this->info('');

        $bar = $this->output->createProgressBar(count($users));
        $bar->start();

        // Processar em lotes de 500
        $chunks = array_chunk($users, 500);
        
        DB::beginTransaction();
        
        try {
            foreach ($chunks as $chunkIndex => $chunk) {
                foreach ($chunk as $userData) {
                    $bar->advance();
                    
                    // Processar usuário
                    $this->processUser($userData, $dryRun);
                }
                
                // Pausar entre lotes
                if ($chunkIndex < count($chunks) - 1 && !$dryRun) {
                    usleep(500000); // 0.5 segundo
                }
            }
            
            if (!$dryRun) {
                DB::commit();
            } else {
                DB::rollBack();
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("\n\n❌ Erro durante migração: " . $e->getMessage());
            return 1;
        }

        $bar->finish();
        $this->info('');
        $this->info('');
        $this->displayResults($dryRun);

        return 0;
    }

    private function loadCSV($file)
    {
        $users = [];
        $handle = fopen($file, 'r');
        
        // Pular header
        $headers = fgetcsv($handle);
        
        while (($data = fgetcsv($handle)) !== FALSE) {
            if (count($data) >= 17) {
                $users[] = [
                    'id_original' => $data[0],
                    'name' => $data[1],
                    'email' => trim(strtolower($data[2])),
                    'created_at' => $data[3],
                    'country' => $data[4],
                    'phone' => $data[5],
                    'phone2' => $data[6],
                    'cpf' => $data[7],
                    'inviter' => $data[8],
                    'inviter_code' => $data[9],
                    'first_name' => $data[10],
                    'last_name' => $data[11],
                    'mother_name' => $data[12],
                    'birth_date' => $data[13],
                    'display_name' => $data[14],
                    'balance' => str_replace(',', '.', $data[15]),
                    'bonus' => str_replace(',', '.', $data[16])
                ];
            }
        }
        
        fclose($handle);
        return $users;
    }

    private function processUser($userData, $dryRun)
    {
        $email = $userData['email'];
        
        // Verificar se email já foi processado nesta sessão
        if (in_array($email, $this->processedEmails)) {
            $this->duplicateCount++;
            $this->duplicateEmails[] = $email;
            return;
        }
        
        // Verificar se email já existe no banco
        if (User::where('email', $email)->exists()) {
            $this->duplicateCount++;
            $this->duplicateEmails[] = $email;
            return;
        }
        
        // Validar email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errorCount++;
            $this->errors[] = "Email inválido: $email";
            return;
        }
        
        // Preparar dados do usuário
        $userModel = [
            'name' => $userData['name'] ?: 'Usuário',
            'email' => $email,
            'password' => Hash::make('trocar@123'),
            'cpf' => $this->cleanCPF($userData['cpf']),
            'phone' => $this->cleanPhone($userData['phone']),
            'last_name' => $userData['last_name'],
            'created_at' => $userData['created_at'] ?: Carbon::now(),
            'updated_at' => Carbon::now(),
            'email_verified_at' => Carbon::now(),
            'role_id' => 3,
            'status' => 'active',
            'inviter_code' => $userData['inviter_code'],
        ];
        
        // Adicionar inviter se existir
        if ($userData['inviter']) {
            $userModel['inviter'] = $userData['inviter'];
        }
        
        if (!$dryRun) {
            try {
                // Criar usuário
                $user = User::create($userModel);
                
                // Criar wallet se tiver saldo
                $balance = floatval($userData['balance']);
                $bonus = floatval($userData['bonus']);
                
                if ($balance > 0 || $bonus > 0) {
                    Wallet::create([
                        'user_id' => $user->id,
                        'balance' => $balance,
                        'balance_bonus' => $bonus,
                        'balance_bonus_rollover' => 0,
                        'balance_withdrawal' => $balance,
                        'currency' => 'BRL',
                        'symbol' => 'R$',
                        'active' => 1,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ]);
                    
                    $this->walletCount++;
                    $this->totalBalance += $balance;
                    $this->totalBonus += $bonus;
                }
                
                $this->successCount++;
                $this->processedEmails[] = $email;
                
            } catch (\Exception $e) {
                $this->errorCount++;
                $this->errors[] = "Erro ao criar usuário $email: " . $e->getMessage();
            }
        } else {
            // Modo dry-run - apenas simular
            $this->successCount++;
            $this->processedEmails[] = $email;
            
            $balance = floatval($userData['balance']);
            $bonus = floatval($userData['bonus']);
            
            if ($balance > 0 || $bonus > 0) {
                $this->walletCount++;
                $this->totalBalance += $balance;
                $this->totalBonus += $bonus;
            }
        }
    }

    private function cleanCPF($cpf)
    {
        if (!$cpf) return null;
        $cleaned = preg_replace('/[^0-9]/', '', $cpf);
        return strlen($cleaned) == 11 ? $cleaned : null;
    }

    private function cleanPhone($phone)
    {
        if (!$phone) return null;
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        return strlen($cleaned) >= 10 ? $cleaned : null;
    }

    private function displayResults($dryRun)
    {
        $this->info('╔════════════════════════════════════════════════╗');
        $this->info('║              RESULTADO DA MIGRAÇÃO             ║');
        $this->info('╚════════════════════════════════════════════════╝');
        $this->info('');
        
        if ($dryRun) {
            $this->warn('🔸 MODO DRY-RUN - Nenhum dado foi inserido!');
            $this->info('');
        }
        
        $this->info('📊 Estatísticas:');
        $this->info('   ✅ Sucesso: ' . number_format($this->successCount, 0, ',', '.') . ' usuários');
        $this->warn('   ⚠️  Duplicados ignorados: ' . number_format($this->duplicateCount, 0, ',', '.') . ' usuários');
        
        if ($this->errorCount > 0) {
            $this->error('   ❌ Erros: ' . number_format($this->errorCount, 0, ',', '.') . ' usuários');
        }
        
        $this->info('');
        $this->info('💰 Saldos migrados:');
        $this->info('   • Wallets criadas: ' . number_format($this->walletCount, 0, ',', '.'));
        $this->info('   • Total Saldo Saque: R$ ' . number_format($this->totalBalance, 2, ',', '.'));
        $this->info('   • Total Saldo Bônus: R$ ' . number_format($this->totalBonus, 2, ',', '.'));
        $this->info('   • Total Geral: R$ ' . number_format($this->totalBalance + $this->totalBonus, 2, ',', '.'));
        
        if ($this->duplicateCount > 0 && $this->duplicateCount <= 30) {
            $this->info('');
            $this->warn('📋 Emails duplicados ignorados:');
            foreach (array_unique($this->duplicateEmails) as $email) {
                $this->warn("   • $email");
            }
        }
        
        if ($this->errorCount > 0 && $this->errorCount <= 10) {
            $this->info('');
            $this->error('❌ Erros encontrados:');
            foreach (array_slice($this->errors, 0, 10) as $error) {
                $this->error("   • $error");
            }
        }
        
        if (!$dryRun) {
            $this->info('');
            $this->info('📊 Total no banco agora: ' . number_format(User::count(), 0, ',', '.') . ' usuários');
            $this->info('');
            
            // Salvar relatório
            $report = "RELATÓRIO DE MIGRAÇÃO - " . Carbon::now()->format('d/m/Y H:i:s') . "\n";
            $report .= str_repeat('=', 50) . "\n";
            $report .= "Usuários migrados: {$this->successCount}\n";
            $report .= "Duplicados ignorados: {$this->duplicateCount}\n";
            $report .= "Erros: {$this->errorCount}\n";
            $report .= "Wallets criadas: {$this->walletCount}\n";
            $report .= "Total Saldo Saque: R$ " . number_format($this->totalBalance, 2, ',', '.') . "\n";
            $report .= "Total Saldo Bônus: R$ " . number_format($this->totalBonus, 2, ',', '.') . "\n";
            $report .= str_repeat('=', 50) . "\n";
            
            if (count($this->duplicateEmails) > 0) {
                $report .= "\nEMAILS DUPLICADOS IGNORADOS:\n";
                foreach (array_unique($this->duplicateEmails) as $email) {
                    $report .= "- $email\n";
                }
            }
            
            file_put_contents(
                base_path('relatorio_migracao_' . date('Ymd_His') . '.txt'),
                $report
            );
            
            $this->info('✅ Relatório salvo em: relatorio_migracao_' . date('Ymd_His') . '.txt');
        }
        
        $this->info('');
        $this->info('✅ Processo concluído!');
    }
}