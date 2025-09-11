<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PlayFiverAgentManager;
use Illuminate\Support\Facades\DB;

class SwitchAgent extends Command
{
    protected $signature = 'casino:switch-agent 
                            {agent? : Nome do agente para ativar} 
                            {--status : Mostrar status de todos os agentes}
                            {--health : Verificar saúde dos agentes}';

    protected $description = 'Gerencia e troca entre agentes PlayFivers';

    public function handle()
    {
        $manager = PlayFiverAgentManager::getInstance();
        
        $this->info("╔════════════════════════════════════════════════════════════════╗");
        $this->info("║            GERENCIADOR DE AGENTES PLAYFIVER                    ║");
        $this->info("╚════════════════════════════════════════════════════════════════╝");
        
        // Se solicitou health check
        if ($this->option('health')) {
            return $this->performHealthCheck($manager);
        }
        
        // Se solicitou status
        if ($this->option('status') || !$this->argument('agent')) {
            return $this->showStatus($manager);
        }
        
        // Trocar agente
        $agentName = $this->argument('agent');
        
        try {
            $this->info("\n🔄 Trocando para agente: {$agentName}");
            
            $manager->switchToAgent($agentName);
            
            $this->info("✅ Agente {$agentName} ativado com sucesso!");
            
            // Mostra novo status
            $this->showStatus($manager);
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error("❌ Erro ao trocar agente: " . $e->getMessage());
            return 1;
        }
    }
    
    private function showStatus($manager)
    {
        $this->info("\n📊 Status dos Agentes:");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        
        $agents = $manager->getAgentsStatus();
        
        foreach ($agents as $agent) {
            $status = $agent->is_active ? '✅' : '❌';
            $priority = $agent->priority == 1 ? '⭐ PRINCIPAL' : "   Backup #{$agent->priority}";
            
            $this->info("");
            $this->info("{$status} {$agent->agent_name} {$priority}");
            $this->info("   Token: " . substr($agent->playfiver_token, 0, 20) . "...");
            $this->info("   Saldo Local: R$ " . number_format($agent->saldo_agente, 2, ',', '.'));
            
            if (isset($agent->saldo_real)) {
                $this->info("   Saldo Real: R$ " . number_format($agent->saldo_real, 2, ',', '.'));
            }
            
            $this->info("   API Status: " . $this->getStatusEmoji($agent->api_status));
            $this->info("   Falhas: {$agent->fail_count}");
            
            if ($agent->last_used) {
                $this->info("   Último uso: {$agent->last_used}");
            }
            
            if ($agent->description) {
                $this->info("   Descrição: {$agent->description}");
            }
        }
        
        $this->info("\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        
        // Mostra agente ativo atual
        $current = $manager->getCurrentAgent();
        if ($current) {
            $this->info("\n🎯 Agente Ativo: {$current->agent_name}");
        }
        
        return 0;
    }
    
    private function performHealthCheck($manager)
    {
        $this->info("\n🏥 Verificando Saúde dos Agentes...");
        $this->info("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        
        $healthStatus = $manager->healthCheck();
        
        foreach ($healthStatus as $agentName => $status) {
            $emoji = $this->getHealthEmoji($status['status']);
            
            $this->info("\n{$emoji} {$agentName}");
            $this->info("   Status: {$status['status']}");
            
            if (isset($status['saldo'])) {
                $this->info("   Saldo: R$ " . number_format($status['saldo'], 2, ',', '.'));
            }
            
            if (isset($status['error'])) {
                $this->error("   Erro: {$status['error']}");
            }
            
            $this->info("   Falhas: {$status['fail_count']}");
            
            if ($status['last_used']) {
                $this->info("   Último uso: {$status['last_used']}");
            }
        }
        
        $this->info("\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        
        // Resumo
        $healthy = count(array_filter($healthStatus, fn($s) => $s['status'] === 'healthy'));
        $total = count($healthStatus);
        
        $this->info("\n📊 Resumo: {$healthy}/{$total} agentes saudáveis");
        
        if ($healthy === 0) {
            $this->error("⚠️ ALERTA: Nenhum agente está funcionando!");
        } elseif ($healthy < $total) {
            $this->warn("⚠️ Alguns agentes estão com problemas");
        } else {
            $this->info("✅ Todos os agentes estão funcionando");
        }
        
        return 0;
    }
    
    private function getStatusEmoji($status)
    {
        return match($status) {
            'online' => '🟢 Online',
            'error' => '🔴 Erro',
            'offline' => '⚫ Offline',
            default => '❓ Desconhecido'
        };
    }
    
    private function getHealthEmoji($status)
    {
        return match($status) {
            'healthy' => '💚',
            'unhealthy' => '💛',
            'error' => '❤️',
            default => '❓'
        };
    }
}