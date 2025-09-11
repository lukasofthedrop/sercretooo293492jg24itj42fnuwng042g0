<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\Order;
use App\Models\Game;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Carbon\Carbon;

class PopulateTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'casino:populate-test-data 
                            {--users=10 : Número de usuários ativos para simular}
                            {--days=7 : Dias de histórico para gerar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Popula o sistema com dados de teste realistas - CIRURGIÃO DEV';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $numUsers = $this->option('users');
        $days = $this->option('days');

        $this->info('╔════════════════════════════════════════════════════════════════╗');
        $this->info('║         GERADOR DE DADOS DE TESTE - CIRURGIÃO DEV             ║');
        $this->info('╚════════════════════════════════════════════════════════════════╝');
        $this->newLine();

        $this->info("📊 Configuração:");
        $this->info("   • Usuários ativos: {$numUsers}");
        $this->info("   • Dias de histórico: {$days}");
        $this->newLine();

        if (!$this->confirm('Isso irá gerar dados de teste no sistema. Continuar?')) {
            $this->warn('Operação cancelada.');
            return 0;
        }

        $this->newLine();
        $bar = $this->output->createProgressBar($numUsers * 3); // 3 operações por usuário

        // Selecionar usuários aleatórios com saldo
        $users = User::whereHas('wallet', function($q) {
            $q->where('balance', '>', 0);
        })->inRandomOrder()->limit($numUsers)->get();

        if ($users->isEmpty()) {
            // Se não há usuários com saldo, pegar qualquer um e dar saldo
            $users = User::inRandomOrder()->limit($numUsers)->get();
        }

        $totalDeposits = 0;
        $totalBets = 0;
        $totalWithdrawals = 0;

        foreach ($users as $user) {
            // PASSO 1: Garantir que tem wallet e fazer depósito inicial
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $user->id],
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

            // Simular depósitos variados nos últimos dias
            $numDeposits = rand(1, 3);
            for ($i = 0; $i < $numDeposits; $i++) {
                $depositAmount = rand(50, 500);
                $depositDate = Carbon::now()->subDays(rand(0, $days));
                
                DB::table('deposits')->insert([
                    'payment_id' => 'TEST_' . uniqid(),
                    'user_id' => $user->id,
                    'amount' => $depositAmount,
                    'type' => ['pix', 'card', 'crypto'][rand(0, 2)],
                    'status' => 1,
                    'currency' => 'BRL',
                    'symbol' => 'R$',
                    'created_at' => $depositDate,
                    'updated_at' => $depositDate
                ]);

                $wallet->balance += $depositAmount;
                $totalDeposits++;
            }
            $bar->advance();

            // PASSO 2: Simular apostas
            $games = Game::inRandomOrder()->limit(5)->get();
            $numBets = rand(5, 20);
            
            for ($i = 0; $i < $numBets && $wallet->balance > 10; $i++) {
                $game = $games->random();
                $betAmount = min($wallet->balance * 0.1, rand(5, 50));
                $isWin = rand(1, 100) <= 35; // 35% chance de ganhar
                $multiplier = $isWin ? (rand(15, 50) / 10) : 0; // 1.5x a 5x
                $winAmount = $isWin ? $betAmount * $multiplier : 0;
                $betDate = Carbon::now()->subDays(rand(0, $days))->subHours(rand(0, 23));

                // Atualizar wallet
                $wallet->balance -= $betAmount;
                if ($isWin) {
                    $wallet->balance += $winAmount;
                    $wallet->total_won++;
                } else {
                    $wallet->total_lose++;
                }
                $wallet->total_bet += $betAmount;

                // Criar order (estrutura correta)
                DB::table('orders')->insert([
                    'user_id' => $user->id,
                    'session_id' => 'TEST_' . uniqid(),
                    'transaction_id' => 'TRX_' . uniqid(),
                    'type' => 'bet',
                    'type_money' => 'balance',
                    'amount' => $betAmount,
                    'game' => $game->game_name,
                    'game_uuid' => $game->game_id,
                    'providers' => $game->provider_id ?? 'test',
                    'refunded' => 0,
                    'status' => $isWin ? 1 : 0,
                    'round_id' => 'ROUND_' . uniqid(),
                    'created_at' => $betDate,
                    'updated_at' => $betDate
                ]);

                // Criar bet_history
                DB::table('bet_histories')->insert([
                    'user_id' => $user->id,
                    'bet_amount' => $betAmount,
                    'payout' => $winAmount,
                    'is_win' => $isWin ? 1 : 0,
                    'stars_revealed' => 0,
                    'bombs_count' => 5,
                    'game_data' => json_encode([
                        'game_id' => $game->id,
                        'game_name' => $game->game_name,
                        'multiplier' => $multiplier
                    ]),
                    'house_profit' => $isWin ? -($winAmount - $betAmount) : $betAmount,
                    'typeWallet' => 'balance',
                    'created_at' => $betDate,
                    'updated_at' => $betDate
                ]);

                $totalBets++;
            }
            $bar->advance();

            // PASSO 3: Simular alguns saques (30% dos usuários)
            if (rand(1, 100) <= 30 && $wallet->balance > 100) {
                $withdrawAmount = $wallet->balance * (rand(30, 70) / 100);
                $withdrawDate = Carbon::now()->subDays(rand(0, max(1, $days - 1)));

                DB::table('withdrawals')->insert([
                    'user_id' => $user->id,
                    'amount' => $withdrawAmount,
                    'type' => 'pix',
                    'status' => rand(1, 100) <= 80 ? 1 : 0, // 80% aprovados
                    'currency' => 'BRL',
                    'symbol' => 'R$',
                    'created_at' => $withdrawDate,
                    'updated_at' => $withdrawDate
                ]);

                if (rand(1, 100) <= 80) { // Se aprovado
                    $wallet->balance -= $withdrawAmount;
                    $wallet->balance_withdrawal += $withdrawAmount;
                }

                $totalWithdrawals++;
            }

            // Salvar wallet atualizada
            $wallet->save();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Estatísticas finais
        $this->info('✅ DADOS DE TESTE GERADOS COM SUCESSO!');
        $this->newLine();

        $this->table(
            ['Tipo', 'Quantidade'],
            [
                ['Usuários Processados', $users->count()],
                ['Depósitos Criados', $totalDeposits],
                ['Apostas Simuladas', $totalBets],
                ['Saques Processados', $totalWithdrawals],
            ]
        );

        // Limpar cache para atualizar dashboard
        Artisan::call('cache:clear');
        
        $this->newLine();
        $this->info('📊 Dashboard atualizado com novos dados!');
        $this->info('🎰 Sistema agora tem atividade simulada realista!');

        return 0;
    }
}