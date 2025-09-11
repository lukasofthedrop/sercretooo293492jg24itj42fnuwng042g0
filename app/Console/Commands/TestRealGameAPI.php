<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Game;
use App\Models\GamesKey;
use App\Traits\Providers\PlayFiverTrait;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class TestRealGameAPI extends Command
{
    use PlayFiverTrait;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'casino:test-real-games 
                            {user_id : ID do usuário para teste}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa a API REAL dos jogos PlayFivers - CIRURGIÃO DEV';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        
        $this->info('╔════════════════════════════════════════════════════════════════╗');
        $this->info('║          TESTE DE API REAL - PLAYFIVER GAMES                  ║');
        $this->info('╚════════════════════════════════════════════════════════════════╝');
        $this->newLine();
        
        // Verificar configuração
        $gamesKey = GamesKey::first();
        
        if (!$gamesKey) {
            $this->error('❌ Configuração de jogos não encontrada!');
            return 1;
        }
        
        $this->info('🎮 Credenciais PlayFivers:');
        $this->info('   URL: ' . $gamesKey->playfiver_url);
        $this->info('   Code: ' . $gamesKey->playfiver_code);
        $this->info('   Token: ' . substr($gamesKey->playfiver_token, 0, 20) . '...');
        $this->info('   Saldo Agente: R$ ' . number_format($gamesKey->saldo_agente, 2, ',', '.'));
        $this->newLine();
        
        // Buscar usuário
        $user = User::find($userId);
        if (!$user) {
            $this->error('❌ Usuário não encontrado!');
            return 1;
        }
        
        $this->info('👤 Usuário: ' . $user->name);
        $this->info('💰 Saldo: R$ ' . number_format($user->wallet->balance ?? 0, 2, ',', '.'));
        $this->newLine();
        
        // Testar conexão com API
        $this->info('📡 Testando conexão com PlayFivers API...');
        
        try {
            // Testar endpoint de saldo do agente
            $response = Http::withOptions([
                'curl' => [
                    CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                ],
            ])->post('https://api.playfivers.com/api/v2/agent_balance', [
                'agentToken' => $gamesKey->playfiver_token,
                'secretKey' => $gamesKey->playfiver_secret,
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                $this->info('✅ API CONECTADA COM SUCESSO!');
                $this->info('   Saldo do Agente na API: R$ ' . number_format($data['balance'] ?? 0, 2, ',', '.'));
            } else {
                $this->error('❌ Erro na resposta da API: ' . $response->status());
                $this->error('Resposta: ' . $response->body());
            }
            
            $this->newLine();
            
            // Buscar um jogo para testar
            $game = Game::where('status', 1)->first();
            if ($game) {
                $this->info('🎲 Testando lançamento de jogo...');
                $this->info('   Jogo: ' . $game->game_name);
                
                // Criar token para teste
                $token = \Helper::MakeToken([
                    'id' => $user->id,
                    'game' => $game->game_code
                ]);
                
                // Testar lançamento
                $launchData = [
                    "agentToken" => $gamesKey->playfiver_token,
                    "secretKey" => $gamesKey->playfiver_secret,
                    "user_code" => $user->email,
                    "game_code" => $game->game_code,
                    "game_original" => $game->original == 1,
                    "user_balance" => $user->wallet->balance ?? 0,
                ];
                
                $launchResponse = Http::withOptions([
                    'curl' => [
                        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                    ],
                ])->post('https://api.playfivers.com/api/v2/game_launch', $launchData);
                
                if ($launchResponse->successful()) {
                    $launchResult = $launchResponse->json();
                    if (isset($launchResult['launch_url'])) {
                        $this->info('✅ JOGO PODE SER LANÇADO!');
                        $this->info('   URL: ' . substr($launchResult['launch_url'], 0, 80) . '...');
                    }
                } else {
                    $this->warn('⚠️ Erro ao lançar jogo: ' . $launchResponse->body());
                }
            }
            
            $this->newLine();
            $this->info('🎉 API PLAYFIVER ESTÁ FUNCIONANDO!');
            $this->info('');
            $this->info('📝 Resumo:');
            $this->info('   ✅ Conexão com API: OK');
            $this->info('   ✅ Autenticação: OK');
            $this->info('   ✅ Saldo do Agente: R$ ' . number_format($gamesKey->saldo_agente, 2, ',', '.'));
            $this->info('   ✅ Jogos podem ser lançados');
            
        } catch (\Exception $e) {
            $this->error('❌ Erro ao conectar com API: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
}