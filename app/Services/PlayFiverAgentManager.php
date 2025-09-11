<?php

namespace App\Services;

use App\Models\GamesKey;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class PlayFiverAgentManager
{
    private static $instance = null;
    private $currentAgent = null;
    private $agents = [];
    
    /**
     * Singleton pattern
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct()
    {
        $this->loadAgents();
    }
    
    /**
     * Carrega todos os agentes disponíveis
     */
    private function loadAgents()
    {
        $this->agents = DB::table('games_keys_backup')
            ->where('is_active', true)
            ->orderBy('priority', 'asc')
            ->get()
            ->toArray();
            
        if (empty($this->agents)) {
            // Fallback para tabela principal se backup estiver vazio
            $mainAgent = GamesKey::first();
            if ($mainAgent) {
                $this->agents[] = (object)[
                    'agent_name' => $mainAgent->playfiver_code,
                    'playfiver_token' => $mainAgent->playfiver_token,
                    'playfiver_secret' => $mainAgent->playfiver_secret,
                    'playfiver_code' => $mainAgent->playfiver_code,
                    'saldo_agente' => $mainAgent->saldo_agente,
                    'priority' => 1,
                    'fail_count' => 0
                ];
            }
        }
    }
    
    /**
     * Obtém o agente atual ativo
     */
    public function getCurrentAgent()
    {
        if ($this->currentAgent === null) {
            $this->currentAgent = $this->agents[0] ?? null;
        }
        return $this->currentAgent;
    }
    
    /**
     * Obtém as credenciais do agente atual
     */
    public function getCredentials()
    {
        $agent = $this->getCurrentAgent();
        
        if (!$agent) {
            throw new \Exception('Nenhum agente disponível');
        }
        
        return [
            'token' => $agent->playfiver_token,
            'secret' => $agent->playfiver_secret,
            'code' => $agent->playfiver_code,
            'saldo' => $agent->saldo_agente
        ];
    }
    
    /**
     * Tenta executar uma operação com fallback automático
     */
    public function executeWithFallback(callable $operation)
    {
        $lastError = null;
        $attemptedAgents = [];
        
        foreach ($this->agents as $agent) {
            $this->currentAgent = $agent;
            $attemptedAgents[] = $agent->agent_name;
            
            try {
                Log::info("Tentando com agente: {$agent->agent_name}");
                
                // Atualiza credenciais no banco principal
                $this->updateMainCredentials($agent);
                
                // Executa operação
                $result = $operation($this->getCredentials());
                
                // Se sucesso, reseta contador de falhas
                $this->resetFailCount($agent->agent_name);
                
                // Atualiza último uso
                DB::table('games_keys_backup')
                    ->where('agent_name', $agent->agent_name)
                    ->update(['last_used' => now()]);
                
                Log::info("Sucesso com agente: {$agent->agent_name}");
                return $result;
                
            } catch (\Exception $e) {
                $lastError = $e;
                $errorMessage = $e->getMessage();
                
                Log::warning("Falha com agente {$agent->agent_name}: {$errorMessage}");
                
                // Incrementa contador de falhas
                $this->incrementFailCount($agent->agent_name);
                
                // Verifica se é erro específico que requer fallback
                if ($this->shouldFallback($errorMessage)) {
                    continue; // Tenta próximo agente
                }
                
                // Se não deve fazer fallback, lança exceção
                throw $e;
            }
        }
        
        // Se chegou aqui, todos os agentes falharam
        Log::error("Todos os agentes falharam: " . implode(', ', $attemptedAgents));
        
        throw new \Exception(
            "Todos os agentes falharam. Último erro: " . 
            ($lastError ? $lastError->getMessage() : 'Erro desconhecido')
        );
    }
    
    /**
     * Verifica se deve tentar fallback baseado no erro
     */
    private function shouldFallback($errorMessage)
    {
        $fallbackErrors = [
            'IP não permitido',
            'IP Não permitido',
            'Unauthorized',
            '401',
            '403',
            'Forbidden',
            'Saldo insuficiente no agente',
            'Agent not found',
            'Invalid token',
            'Invalid credentials'
        ];
        
        foreach ($fallbackErrors as $error) {
            if (stripos($errorMessage, $error) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Atualiza credenciais na tabela principal
     */
    private function updateMainCredentials($agent)
    {
        DB::table('games_keys')
            ->where('id', 1)
            ->update([
                'playfiver_code' => $agent->playfiver_code,
                'playfiver_token' => $agent->playfiver_token,
                'playfiver_secret' => $agent->playfiver_secret,
                'saldo_agente' => $agent->saldo_agente,
                'updated_at' => now()
            ]);
    }
    
    /**
     * Incrementa contador de falhas
     */
    private function incrementFailCount($agentName)
    {
        DB::table('games_keys_backup')
            ->where('agent_name', $agentName)
            ->increment('fail_count');
            
        // Se muitas falhas, desativa agente temporariamente
        $agent = DB::table('games_keys_backup')
            ->where('agent_name', $agentName)
            ->first();
            
        if ($agent && $agent->fail_count >= 10) {
            Log::warning("Agente {$agentName} desativado após 10 falhas");
            
            DB::table('games_keys_backup')
                ->where('agent_name', $agentName)
                ->update(['is_active' => false]);
                
            // Recarrega agentes
            $this->loadAgents();
        }
    }
    
    /**
     * Reseta contador de falhas
     */
    private function resetFailCount($agentName)
    {
        DB::table('games_keys_backup')
            ->where('agent_name', $agentName)
            ->update(['fail_count' => 0]);
    }
    
    /**
     * Troca manualmente para um agente específico
     */
    public function switchToAgent($agentName)
    {
        $agent = DB::table('games_keys_backup')
            ->where('agent_name', $agentName)
            ->first();
            
        if (!$agent) {
            throw new \Exception("Agente {$agentName} não encontrado");
        }
        
        // Ativa o agente se estiver desativado
        if (!$agent->is_active) {
            DB::table('games_keys_backup')
                ->where('agent_name', $agentName)
                ->update([
                    'is_active' => true,
                    'fail_count' => 0
                ]);
        }
        
        // Define como prioridade 1
        DB::table('games_keys_backup')
            ->where('agent_name', $agentName)
            ->update(['priority' => 1]);
            
        // Ajusta prioridades dos outros
        DB::table('games_keys_backup')
            ->where('agent_name', '!=', $agentName)
            ->increment('priority');
            
        // Atualiza credenciais principais
        $this->updateMainCredentials($agent);
        
        // Recarrega agentes
        $this->loadAgents();
        
        return true;
    }
    
    /**
     * Obtém status de todos os agentes
     */
    public function getAgentsStatus()
    {
        return DB::table('games_keys_backup')
            ->orderBy('priority', 'asc')
            ->get()
            ->map(function ($agent) {
                // Tenta verificar saldo real na API
                try {
                    $response = Http::withOptions([
                        'force_ip_resolve' => 'v4',
                    ])->get('https://api.playfivers.com/api/v2/agent', [
                        'agentToken' => $agent->playfiver_token,
                        'secretKey' => $agent->playfiver_secret,
                    ]);
                    
                    if ($response->successful()) {
                        $data = $response->json();
                        $agent->saldo_real = $data['data']['balance'] ?? 0;
                        $agent->api_status = 'online';
                    } else {
                        $agent->saldo_real = null;
                        $agent->api_status = 'error';
                    }
                } catch (\Exception $e) {
                    $agent->saldo_real = null;
                    $agent->api_status = 'offline';
                }
                
                return $agent;
            });
    }
    
    /**
     * Verifica saúde dos agentes
     */
    public function healthCheck()
    {
        $status = [];
        
        foreach ($this->agents as $agent) {
            try {
                $response = Http::withOptions([
                    'force_ip_resolve' => 'v4',
                    'timeout' => 5
                ])->get('https://api.playfivers.com/api/v2/agent', [
                    'agentToken' => $agent->playfiver_token,
                    'secretKey' => $agent->playfiver_secret,
                ]);
                
                $status[$agent->agent_name] = [
                    'status' => $response->successful() ? 'healthy' : 'unhealthy',
                    'saldo' => $response->successful() ? 
                        ($response->json()['data']['balance'] ?? 0) : 0,
                    'fail_count' => $agent->fail_count,
                    'last_used' => $agent->last_used
                ];
                
            } catch (\Exception $e) {
                $status[$agent->agent_name] = [
                    'status' => 'error',
                    'error' => $e->getMessage(),
                    'fail_count' => $agent->fail_count,
                    'last_used' => $agent->last_used
                ];
            }
        }
        
        return $status;
    }
}