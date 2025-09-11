<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SimulateWithdrawal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'casino:simulate-withdrawal 
                            {user_id : ID do usuário} 
                            {amount : Valor do saque}
                            {--type=pix : Tipo de saque (pix, bank, crypto)}
                            {--approve : Aprovar automaticamente o saque}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simula um saque do sistema - CIRURGIÃO DEV';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $amount = floatval($this->argument('amount'));
        $type = $this->option('type');
        $autoApprove = $this->option('approve');

        $this->info('╔════════════════════════════════════════════════════════════════╗');
        $this->info('║              SIMULADOR DE SAQUES - CIRURGIÃO DEV              ║');
        $this->info('╚════════════════════════════════════════════════════════════════╝');
        $this->newLine();

        // Verificar usuário
        $user = User::find($userId);
        if (!$user) {
            $this->error("❌ Usuário ID {$userId} não encontrado!");
            return 1;
        }

        // Verificar wallet
        $wallet = Wallet::where('user_id', $userId)->first();
        if (!$wallet) {
            $this->error("❌ Usuário não possui wallet!");
            return 1;
        }

        if ($wallet->balance < $amount) {
            $this->error("❌ Saldo insuficiente!");
            $this->error("   Saldo disponível: R$ " . number_format($wallet->balance, 2, ',', '.'));
            $this->error("   Valor solicitado: R$ " . number_format($amount, 2, ',', '.'));
            return 1;
        }

        $this->info("👤 Usuário: {$user->name} ({$user->email})");
        $this->info("💰 Saldo Atual: R$ " . number_format($wallet->balance, 2, ',', '.'));
        $this->info("💸 Valor do Saque: R$ " . number_format($amount, 2, ',', '.'));
        $this->info("💳 Método: " . strtoupper($type));
        $this->info("📋 Status: " . ($autoApprove ? "Será APROVADO automaticamente" : "Pendente de aprovação"));
        $this->newLine();

        if (!$this->confirm('Confirma simulação do saque?')) {
            $this->warn('Operação cancelada.');
            return 0;
        }

        DB::beginTransaction();
        try {
            $oldBalance = $wallet->balance;
            
            // Se aprovado automaticamente, debitar do saldo
            if ($autoApprove) {
                $wallet->balance -= $amount;
                // Adicionar ao balance_withdrawal para controle
                $wallet->balance_withdrawal += $amount;
                $wallet->save();
            }

            // Criar registro de saque
            $withdrawal = Withdrawal::create([
                'user_id' => $userId,
                'amount' => $amount,
                'type' => $type,
                'status' => $autoApprove ? 1 : 0, // 1 = aprovado, 0 = pendente
                'currency' => 'BRL',
                'symbol' => 'R$',
                'proof' => json_encode([
                    'method' => $type,
                    'simulated' => true,
                    'pix_key' => $type === 'pix' ? $user->email : null,
                    'bank_account' => $type === 'bank' ? '12345-6' : null,
                    'crypto_wallet' => $type === 'crypto' ? 'bc1qxy2kgdygjrsqtzq2n0yrf2493p83kkfjhx0wlh' : null
                ])
            ]);

            // Se aprovado, criar transação
            if ($autoApprove && class_exists(\App\Models\Transaction::class)) {
                \App\Models\Transaction::create([
                    'payment_id' => 'WD_' . strtoupper(uniqid()),
                    'user_id' => $userId,
                    'payment_method' => $type,
                    'price' => $amount,
                    'currency' => 'BRL',
                    'status' => 1,
                    'idUnico' => uniqid('WITHDRAW_')
                ]);
            }

            DB::commit();

            $this->newLine();
            $this->info('✅ SAQUE SIMULADO COM SUCESSO!');
            $this->newLine();
            
            $statusText = $autoApprove ? 'Aprovado ✅' : 'Pendente ⏳';
            $this->table(
                ['Campo', 'Valor'],
                [
                    ['ID Saque', $withdrawal->id],
                    ['Método', strtoupper($type)],
                    ['Valor', 'R$ ' . number_format($amount, 2, ',', '.')],
                    ['Status', $statusText],
                    ['Saldo Anterior', 'R$ ' . number_format($oldBalance, 2, ',', '.')],
                    ['Saldo Atual', 'R$ ' . number_format($wallet->balance, 2, ',', '.')],
                    ['Total Sacado', 'R$ ' . number_format($wallet->balance_withdrawal, 2, ',', '.')],
                ]
            );

            if (!$autoApprove) {
                $this->newLine();
                $this->warn('⚠️ Saque criado como PENDENTE. Para aprovar, use:');
                $this->info("   php artisan casino:approve-withdrawal {$withdrawal->id}");
            }

            $this->newLine();
            $this->info('📊 Dashboard atualizado com nova transação!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Erro ao simular saque: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}