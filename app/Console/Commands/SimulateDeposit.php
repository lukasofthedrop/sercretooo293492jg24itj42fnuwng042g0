<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Deposit;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SimulateDeposit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'casino:simulate-deposit 
                            {user_id : ID do usuário} 
                            {amount : Valor do depósito}
                            {--type=pix : Tipo de pagamento (pix, card, crypto)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simula um depósito para teste do sistema - CIRURGIÃO DEV';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $amount = floatval($this->argument('amount'));
        $type = $this->option('type');

        $this->info('╔════════════════════════════════════════════════════════════════╗');
        $this->info('║            SIMULADOR DE DEPÓSITO - CIRURGIÃO DEV              ║');
        $this->info('╚════════════════════════════════════════════════════════════════╝');
        $this->newLine();

        // Verificar usuário
        $user = User::find($userId);
        if (!$user) {
            $this->error("❌ Usuário ID {$userId} não encontrado!");
            return 1;
        }

        $this->info("👤 Usuário: {$user->name} ({$user->email})");
        $this->info("💰 Valor: R$ " . number_format($amount, 2, ',', '.'));
        $this->info("💳 Tipo: " . strtoupper($type));
        $this->newLine();

        if (!$this->confirm('Confirma simulação do depósito?')) {
            $this->warn('Operação cancelada.');
            return 0;
        }

        DB::beginTransaction();
        try {
            // Criar ou obter wallet
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $userId],
                [
                    'currency' => 'BRL',
                    'symbol' => 'R$',
                    'balance' => 0,
                    'balance_bonus' => 0,
                    'balance_withdrawal' => 0,
                    'refer_rewards' => 0,
                    'active' => 1
                ]
            );

            $oldBalance = $wallet->balance;

            // Criar depósito
            $deposit = Deposit::create([
                'payment_id' => 'SIM_' . strtoupper(uniqid()),
                'user_id' => $userId,
                'amount' => $amount,
                'type' => $type,
                'status' => 1, // Aprovado
                'currency' => 'BRL',
                'symbol' => 'R$',
                'proof' => 'Depósito simulado para teste'
            ]);

            // Atualizar saldo da wallet
            $wallet->balance += $amount;
            $wallet->save();

            // Criar transação (usando estrutura existente)
            if (class_exists(\App\Models\Transaction::class)) {
                \App\Models\Transaction::create([
                    'payment_id' => $deposit->payment_id,
                    'user_id' => $userId,
                    'payment_method' => $type,
                    'price' => $amount,
                    'currency' => 'BRL',
                    'status' => 1,
                    'idUnico' => uniqid('DEP_')
                ]);
            }

            DB::commit();

            $this->newLine();
            $this->info('✅ DEPÓSITO SIMULADO COM SUCESSO!');
            $this->newLine();
            $this->table(
                ['Campo', 'Valor'],
                [
                    ['ID Depósito', $deposit->id],
                    ['Payment ID', $deposit->payment_id],
                    ['Saldo Anterior', 'R$ ' . number_format($oldBalance, 2, ',', '.')],
                    ['Valor Depositado', 'R$ ' . number_format($amount, 2, ',', '.')],
                    ['Saldo Atual', 'R$ ' . number_format($wallet->balance, 2, ',', '.')],
                    ['Status', 'Aprovado ✅'],
                ]
            );

            $this->newLine();
            $this->info('📊 Dashboard atualizado com nova transação!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Erro ao simular depósito: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}