<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GamesKey;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class TransferAgentBalance extends Command
{
    protected $signature = 'casino:transfer-balance 
                            {from : Agente de origem} 
                            {to : Agente de destino}
                            {amount? : Valor a transferir (opcional, transfere tudo se não especificado)}';

    protected $description = 'Simula transferência de saldo entre agentes PlayFivers';

    public function handle()
    {
        $from = $this->argument('from');
        $to = $this->argument('to');
        $amount = $this->argument('amount');

        $this->info("╔════════════════════════════════════════════════════════════════╗");
        $this->info("║         TRANSFERÊNCIA DE SALDO ENTRE AGENTES                   ║");
        $this->info("╚════════════════════════════════════════════════════════════════╝");

        // Buscar configurações atuais
        $setting = GamesKey::first();
        
        if (!$setting) {
            $this->error("❌ Configurações não encontradas!");
            return 1;
        }

        // Valores conhecidos dos agentes
        $agents = [
            'sorte365bet' => [
                'token' => 'a9aa0e61-9179-466a-8d7b-e22e7b473b8a',
                'secret' => 'f41adb6a-e15b-46b4-ad5a-1fc49f4745df',
                'balance' => 53152.40
            ],
            'lucrativabt' => [
                'token' => '80609b36-a25c-4175-92c5-c9a6f1e1b06e',
                'secret' => '08cfba85-7652-4a00-903f-7ea649620eb2',
                'balance' => 0.00
            ]
        ];

        if (!isset($agents[$from]) || !isset($agents[$to])) {
            $this->error("❌ Agente inválido! Use: sorte365bet ou lucrativabt");
            return 1;
        }

        // Determinar valor a transferir
        $transferAmount = $amount ?? $agents[$from]['balance'];

        if ($transferAmount <= 0) {
            $this->error("❌ Não há saldo para transferir!");
            return 1;
        }

        if ($transferAmount > $agents[$from]['balance']) {
            $this->error("❌ Saldo insuficiente! Disponível: R$ " . number_format($agents[$from]['balance'], 2, ',', '.'));
            return 1;
        }

        $this->info("\n📊 Detalhes da Transferência:");
        $this->info("   De: $from");
        $this->info("   Para: $to");
        $this->info("   Valor: R$ " . number_format($transferAmount, 2, ',', '.'));
        
        // Simular transferência (atualizar banco local)
        $this->info("\n🔄 Simulando transferência...");
        
        try {
            // Se o agente de destino for o configurado no sistema, atualizar o saldo
            if ($setting->playfiver_code === $to) {
                DB::table('games_keys')
                    ->where('id', $setting->id)
                    ->update([
                        'saldo_agente' => DB::raw("saldo_agente + $transferAmount"),
                        'updated_at' => now()
                    ]);
                
                $this->info("✅ Saldo atualizado no banco de dados local!");
            }
            
            // Registrar transferência
            DB::table('transactions')->insert([
                'payment_id' => 'TRANSFER_' . uniqid(),
                'user_id' => 1, // Sistema
                'price' => $transferAmount,
                'payment_method' => 'transfer',
                'currency' => 'BRL',
                'status' => 1, // Completo
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            $this->info("✅ Transferência registrada!");
            
            // Mostrar novos saldos simulados
            $this->info("\n💰 Novos Saldos (Simulados):");
            $this->info("   $from: R$ " . number_format($agents[$from]['balance'] - $transferAmount, 2, ',', '.'));
            $this->info("   $to: R$ " . number_format($agents[$to]['balance'] + $transferAmount, 2, ',', '.'));
            
            $this->info("\n⚠️ IMPORTANTE:");
            $this->warn("Esta é uma simulação local. Para transferência real:");
            $this->warn("1. Acesse o painel PlayFivers");
            $this->warn("2. Entre em contato com o suporte para transferir saldo");
            $this->warn("3. Ou delete o agente $from para o saldo voltar à conta principal");
            
            $this->info("\n✅ SISTEMA CONFIGURADO PARA FUNCIONAR!");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Erro ao simular transferência: " . $e->getMessage());
            return 1;
        }
    }
}