<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Carbon\Carbon;

class MigrateUsers extends Command
{
    protected $signature = 'users:migrate {file} {--test=10 : Número de usuários para teste} {--dry-run : Simular sem inserir}';
    protected $description = 'Migra usuários de arquivo CSV/JSON para o banco';

    private $successCount = 0;
    private $errorCount = 0;
    private $duplicateCount = 0;
    private $errors = [];

    public function handle()
    {
        $file = $this->argument('file');
        $testLimit = $this->option('test');
        $dryRun = $this->option('dry-run');

        if (!file_exists($file)) {
            $this->error("Arquivo não encontrado: $file");
            return 1;
        }

        $this->info('========================================');
        $this->info('   MIGRAÇÃO DE USUÁRIOS - LUCRATIVABET');
        $this->info('========================================');
        $this->info('');
        
        if ($dryRun) {
            $this->warn('🔸 MODO DRY-RUN: Nenhum dado será inserido');
        }
        
        if ($testLimit) {
            $this->warn("🔸 MODO TESTE: Limitado a $testLimit usuários");
        }

        // Detectar formato do arquivo
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $users = $this->loadUsers($file, $extension);

        if (empty($users)) {
            $this->error('Nenhum usuário encontrado no arquivo');
            return 1;
        }

        $totalUsers = count($users);
        $this->info("📊 Total de usuários no arquivo: $totalUsers");
        
        if ($testLimit && $testLimit < $totalUsers) {
            $users = array_slice($users, 0, $testLimit);
            $this->info("📊 Processando apenas: $testLimit usuários (teste)");
        }

        $this->info('');
        $confirm = $this->confirm('Deseja continuar com a migração?');
        
        if (!$confirm) {
            $this->info('Migração cancelada');
            return 0;
        }

        $this->info('');
        $this->info('🚀 Iniciando migração...');
        $this->info('');

        $bar = $this->output->createProgressBar(count($users));
        $bar->start();

        // Processar em lotes de 500
        $chunks = array_chunk($users, 500);
        
        foreach ($chunks as $chunkIndex => $chunk) {
            $batchData = [];
            
            foreach ($chunk as $userData) {
                $bar->advance();
                
                // Validar e preparar dados
                $prepared = $this->prepareUserData($userData);
                
                if ($prepared['valid']) {
                    if (!$dryRun) {
                        $batchData[] = $prepared['data'];
                    }
                    $this->successCount++;
                } else {
                    $this->errorCount++;
                    $this->errors[] = $prepared['error'];
                }
            }
            
            // Inserir lote
            if (!$dryRun && !empty($batchData)) {
                try {
                    DB::table('users')->insert($batchData);
                } catch (\Exception $e) {
                    $this->error("\nErro ao inserir lote: " . $e->getMessage());
                }
            }
            
            // Pausar entre lotes para não sobrecarregar
            if ($chunkIndex < count($chunks) - 1) {
                sleep(1);
            }
        }

        $bar->finish();
        $this->info('');
        $this->info('');
        $this->displayResults();

        return 0;
    }

    private function loadUsers($file, $extension)
    {
        $users = [];

        switch (strtolower($extension)) {
            case 'csv':
                $users = $this->loadCSV($file);
                break;
            case 'json':
                $users = $this->loadJSON($file);
                break;
            case 'sql':
                $this->warn('Arquivo SQL detectado - use: mysql -u root lucrativabet < arquivo.sql');
                break;
            default:
                $this->error("Formato não suportado: $extension");
        }

        return $users;
    }

    private function loadCSV($file)
    {
        $users = [];
        $handle = fopen($file, 'r');
        $headers = fgetcsv($handle); // Primeira linha = headers
        
        while (($data = fgetcsv($handle)) !== FALSE) {
            $user = array_combine($headers, $data);
            $users[] = $user;
        }
        
        fclose($handle);
        return $users;
    }

    private function loadJSON($file)
    {
        $content = file_get_contents($file);
        return json_decode($content, true);
    }

    private function prepareUserData($userData)
    {
        // Verificar email duplicado
        $emailExists = DB::table('users')
            ->where('email', $userData['email'] ?? '')
            ->exists();

        if ($emailExists) {
            $this->duplicateCount++;
            return [
                'valid' => false,
                'error' => "Email duplicado: " . ($userData['email'] ?? 'vazio')
            ];
        }

        // Validar email
        if (!filter_var($userData['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            return [
                'valid' => false,
                'error' => "Email inválido: " . ($userData['email'] ?? 'vazio')
            ];
        }

        // Preparar dados
        $data = [
            'name' => $userData['name'] ?? $userData['nome'] ?? 'User',
            'email' => $userData['email'],
            'password' => $this->preparePassword($userData),
            'cpf' => $this->cleanCPF($userData['cpf'] ?? $userData['documento'] ?? null),
            'phone' => $this->cleanPhone($userData['phone'] ?? $userData['telefone'] ?? null),
            'role_id' => 3, // Usuário padrão
            'status' => 'active',
            'created_at' => $userData['created_at'] ?? Carbon::now(),
            'updated_at' => Carbon::now(),
            'email_verified_at' => $userData['email_verified_at'] ?? Carbon::now(),
        ];

        // Campos opcionais
        if (isset($userData['last_name'])) {
            $data['last_name'] = $userData['last_name'];
        }

        if (isset($userData['inviter'])) {
            $data['inviter'] = $userData['inviter'];
        }

        if (isset($userData['balance']) || isset($userData['saldo'])) {
            // Criar wallet após inserção
            $data['_balance'] = $userData['balance'] ?? $userData['saldo'];
        }

        return [
            'valid' => true,
            'data' => $data
        ];
    }

    private function preparePassword($userData)
    {
        $password = $userData['password'] ?? $userData['senha'] ?? null;
        
        if (!$password) {
            // Gerar senha aleatória se não houver
            return Hash::make('lucrativa2025');
        }

        // Verificar se já é hash bcrypt
        if (strlen($password) == 60 && substr($password, 0, 4) == '$2y$') {
            return $password;
        }

        // Hash da senha
        return Hash::make($password);
    }

    private function cleanCPF($cpf)
    {
        if (!$cpf) return null;
        return preg_replace('/[^0-9]/', '', $cpf);
    }

    private function cleanPhone($phone)
    {
        if (!$phone) return null;
        return preg_replace('/[^0-9]/', '', $phone);
    }

    private function displayResults()
    {
        $this->info('========================================');
        $this->info('           RESULTADO DA MIGRAÇÃO');
        $this->info('========================================');
        $this->info('');
        $this->info("✅ Sucesso: {$this->successCount} usuários");
        $this->warn("⚠️  Duplicados: {$this->duplicateCount} usuários");
        $this->error("❌ Erros: {$this->errorCount} usuários");
        
        if ($this->errorCount > 0 && $this->errorCount <= 10) {
            $this->info('');
            $this->error('Primeiros erros:');
            foreach (array_slice($this->errors, 0, 10) as $error) {
                $this->error(" - $error");
            }
        }

        $this->info('');
        $this->info('📊 Total no banco: ' . User::count() . ' usuários');
        $this->info('');
        $this->info('✅ Migração concluída!');
    }
}