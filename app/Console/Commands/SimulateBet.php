<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Order;
use App\Models\Game;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SimulateBet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'casino:simulate-bet 
                            {user_id : ID do usuário} 
                            {amount : Valor da aposta}
                            {--game_id= : ID do jogo (aleatório se não especificado)}
                            {--win : Se a aposta deve ganhar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simula uma aposta no cassino - CIRURGIÃO DEV';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $amount = floatval($this->argument('amount'));
        $gameId = $this->option('game_id');
        $isWin = $this->option('win');

        $this->info('╔════════════════════════════════════════════════════════════════╗');
        $this->info('║              SIMULADOR DE APOSTAS - CIRURGIÃO DEV             ║');
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
            $this->error("❌ Usuário não possui wallet! Execute um depósito primeiro.");
            return 1;
        }

        if ($wallet->balance < $amount) {
            $this->error("❌ Saldo insuficiente! Saldo: R$ {$wallet->balance} | Aposta: R$ {$amount}");
            return 1;
        }

        // Selecionar jogo
        if (!$gameId) {
            $game = Game::inRandomOrder()->first();
            if (!$game) {
                $this->error("❌ Nenhum jogo encontrado no sistema!");
                return 1;
            }
            $gameId = $game->id;
        } else {
            $game = Game::find($gameId);
            if (!$game) {
                $this->error("❌ Jogo ID {$gameId} não encontrado!");
                return 1;
            }
        }

        // Calcular resultado
        $multiplier = $isWin ? rand(2, 10) / 1.0 : 0;
        $winAmount = $isWin ? $amount * $multiplier : 0;
        $profit = $winAmount - $amount;

        $this->info("👤 Usuário: {$user->name}");
        $this->info("🎮 Jogo: {$game->game_name}");
        $this->info("💰 Aposta: R$ " . number_format($amount, 2, ',', '.'));
        $this->info("🎲 Resultado: " . ($isWin ? "GANHOU! 🎉" : "PERDEU 😔"));
        if ($isWin) {
            $this->info("✨ Multiplicador: {$multiplier}x");
            $this->info("💵 Ganhos: R$ " . number_format($winAmount, 2, ',', '.'));
        }
        $this->newLine();

        if (!$this->confirm('Confirma simulação da aposta?')) {
            $this->warn('Operação cancelada.');
            return 0;
        }

        DB::beginTransaction();
        try {
            $oldBalance = $wallet->balance;

            // Debitar aposta
            $wallet->balance -= $amount;
            
            // Se ganhou, creditar prêmio
            if ($isWin) {
                $wallet->balance += $winAmount;
                $wallet->total_won += 1;
                $wallet->last_won = 1;
                $wallet->last_lose = 0;
            } else {
                $wallet->total_lose += 1;
                $wallet->last_lose = 1;
                $wallet->last_won = 0;
            }
            
            // Atualizar total apostado
            $wallet->total_bet += $amount;
            $wallet->save();

            // Criar order de aposta (estrutura correta)
            $order = Order::create([
                'user_id' => $userId,
                'session_id' => uniqid('BET_'),
                'transaction_id' => uniqid('TRX_'),
                'type' => 'bet',
                'type_money' => 'balance',
                'amount' => $amount,
                'game' => $game->game_name,
                'game_uuid' => $game->game_id,
                'providers' => $game->provider_id ?? 'test',
                'refunded' => 0,
                'status' => $isWin ? 1 : 0,
                'round_id' => uniqid('ROUND_')
            ]);

            // Se ganhou, criar order de win
            if ($isWin) {
                Order::create([
                    'user_id' => $userId,
                    'session_id' => $order->session_id,
                    'transaction_id' => uniqid('WIN_'),
                    'type' => 'win',
                    'type_money' => 'balance',
                    'amount' => $winAmount,
                    'game' => $game->game_name,
                    'game_uuid' => $game->game_id,
                    'providers' => $game->provider_id ?? 'test',
                    'refunded' => 0,
                    'status' => 1,
                    'round_id' => $order->round_id
                ]);
            }

            // Registrar na bet_histories (estrutura específica)
            DB::table('bet_histories')->insert([
                'user_id' => $userId,
                'bet_amount' => $amount,
                'payout' => $winAmount,
                'is_win' => $isWin ? 1 : 0,
                'stars_revealed' => 0,
                'bombs_count' => 5,
                'game_data' => json_encode([
                    'game_id' => $gameId,
                    'game_name' => $game->game_name,
                    'multiplier' => $multiplier
                ]),
                'house_profit' => $isWin ? -$profit : $amount,
                'typeWallet' => 'balance',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            $this->newLine();
            $this->info('✅ APOSTA SIMULADA COM SUCESSO!');
            $this->newLine();
            $this->table(
                ['Campo', 'Valor'],
                [
                    ['ID Order', $order->id],
                    ['Saldo Anterior', 'R$ ' . number_format($oldBalance, 2, ',', '.')],
                    ['Valor Apostado', 'R$ ' . number_format($amount, 2, ',', '.')],
                    ['Resultado', $isWin ? 'GANHOU 🎉' : 'PERDEU 😔'],
                    ['Ganhos', 'R$ ' . number_format($winAmount, 2, ',', '.')],
                    ['Lucro/Prejuízo', 'R$ ' . number_format($profit, 2, ',', '.')],
                    ['Saldo Atual', 'R$ ' . number_format($wallet->balance, 2, ',', '.')],
                ]
            );

            $this->newLine();
            $this->info('📊 Estatísticas da wallet atualizadas!');
            $this->info("   Total Apostado: R$ " . number_format($wallet->total_bet, 2, ',', '.'));
            $this->info("   Vitórias: {$wallet->total_won}");
            $this->info("   Derrotas: {$wallet->total_lose}");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Erro ao simular aposta: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}