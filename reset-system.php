<?php

/**
 * SISTEMA DE RESET SEGURO - LUCRATIVABET
 * 
 * Este script faz backup e reset dos dados de teste
 * mantendo apenas estrutura e usuários administrativos
 */

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ResetSystem {
    
    private $backupDir;
    private $timestamp;
    
    public function __construct() {
        $this->timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $this->backupDir = storage_path('backups/reset_' . $this->timestamp);
        
        if (!file_exists($this->backupDir)) {
            mkdir($this->backupDir, 0777, true);
        }
    }
    
    /**
     * Executar reset completo
     */
    public function execute($mode = 'safe') {
        echo "\n========================================\n";
        echo "  SISTEMA DE RESET LUCRATIVABET\n";
        echo "========================================\n\n";
        
        // 1. Fazer backup
        echo "📦 Criando backup de segurança...\n";
        $this->createBackup();
        
        // 2. Confirmar ação
        if ($mode === 'safe') {
            echo "\n⚠️  ATENÇÃO: Isso removerá todos os dados de teste!\n";
            echo "Deseja continuar? (sim/não): ";
            $confirm = trim(fgets(STDIN));
            if (strtolower($confirm) !== 'sim') {
                echo "❌ Operação cancelada.\n";
                return;
            }
        }
        
        // 3. Resetar dados
        echo "\n🔄 Resetando dados de teste...\n";
        $this->resetData();
        
        // 4. Limpar cache
        echo "🧹 Limpando cache...\n";
        $this->clearCache();
        
        // 5. Verificar resultado
        echo "\n✅ Reset concluído com sucesso!\n";
        $this->showStatus();
        
        echo "\n📂 Backup salvo em: " . $this->backupDir . "\n";
        echo "\n========================================\n";
    }
    
    /**
     * Criar backup completo
     */
    private function createBackup() {
        $tables = [
            'users' => DB::table('users')->get(),
            'deposits' => DB::table('deposits')->get(),
            'orders' => DB::table('orders')->get(),
            'withdrawals' => DB::table('withdrawals')->get(),
            'wallets' => DB::table('wallets')->get(),
            'affiliate_histories' => DB::table('affiliate_histories')->get(),
            'transactions' => DB::table('transactions')->get(),
        ];
        
        foreach ($tables as $table => $data) {
            $file = $this->backupDir . '/' . $table . '.json';
            file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
            echo "  ✓ Backup da tabela '$table': " . count($data) . " registros\n";
        }
        
        // Backup do .env
        copy(base_path('.env'), $this->backupDir . '/.env.backup');
        echo "  ✓ Backup do arquivo .env\n";
    }
    
    /**
     * Resetar dados mantendo apenas admins
     */
    private function resetData() {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        try {
            // IDs dos admins a preservar - INCLUÍDO lucrativa@bet.com
            $adminEmails = ['lucrativa@bet.com', 'admin@admin.com', 'admin@lucrativabet.com', 'dev@lucrativabet.com'];
            $adminIds = DB::table('users')
                ->whereIn('email', $adminEmails)
                ->pluck('id')
                ->toArray();
            
            // Limpar tabelas de transações
            DB::table('deposits')->truncate();
            echo "  ✓ Tabela 'deposits' limpa\n";
            
            DB::table('orders')->truncate();
            echo "  ✓ Tabela 'orders' limpa\n";
            
            DB::table('withdrawals')->truncate();
            echo "  ✓ Tabela 'withdrawals' limpa\n";
            
            DB::table('transactions')->truncate();
            echo "  ✓ Tabela 'transactions' limpa\n";
            
            DB::table('affiliate_histories')->truncate();
            echo "  ✓ Tabela 'affiliate_histories' limpa\n";
            
            // Resetar carteiras (manter apenas dos admins com saldo zero)
            DB::table('wallets')->whereNotIn('user_id', $adminIds)->delete();
            DB::table('wallets')->whereIn('user_id', $adminIds)->update([
                'balance' => 0,
                'balance_bonus' => 0,
                'balance_withdrawal' => 0,
                'total_bet' => 0,
                'total_won' => 0,
                'total_lose' => 0,
                'last_won' => 0,
                'last_lose' => 0,
            ]);
            echo "  ✓ Carteiras resetadas\n";
            
            // Remover usuários de teste (manter apenas admins)
            DB::table('users')->whereNotIn('id', $adminIds)->delete();
            echo "  ✓ Usuários de teste removidos (mantidos " . count($adminIds) . " admins)\n";
            
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
    
    /**
     * Limpar todo o cache
     */
    private function clearCache() {
        // Limpar cache do Laravel
        \Artisan::call('cache:clear');
        echo "  ✓ Cache do aplicativo limpo\n";
        
        \Artisan::call('config:clear');
        echo "  ✓ Cache de configuração limpo\n";
        
        \Artisan::call('view:clear');
        echo "  ✓ Cache de views limpo\n";
        
        // Limpar cache específico dos widgets
        $cacheKeys = [
            'stats_financial_*',
            'stats_player_balance',
            'stats_affiliate_rewards',
            'top5_games_chart_data',
            'users_ranking_chart_data',
            'wallet_overview_*'
        ];
        
        foreach ($cacheKeys as $key) {
            \Cache::forget($key);
        }
        echo "  ✓ Cache dos widgets limpo\n";
    }
    
    /**
     * Mostrar status após reset
     */
    private function showStatus() {
        echo "\n📊 STATUS APÓS RESET:\n";
        echo "====================\n";
        echo "Usuários: " . DB::table('users')->count() . " (apenas admins)\n";
        echo "Depósitos: " . DB::table('deposits')->count() . "\n";
        echo "Apostas: " . DB::table('orders')->where('type', 'bet')->count() . "\n";
        echo "Saques: " . DB::table('withdrawals')->count() . "\n";
        echo "Carteiras: " . DB::table('wallets')->count() . "\n";
        echo "Saldo total: R$ " . number_format(DB::table('wallets')->sum('balance'), 2, ',', '.') . "\n";
    }
    
    /**
     * Restaurar backup
     */
    public function restore($backupPath) {
        echo "\n🔄 Restaurando backup...\n";
        
        if (!file_exists($backupPath)) {
            echo "❌ Backup não encontrado: $backupPath\n";
            return;
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        try {
            $tables = ['users', 'deposits', 'orders', 'withdrawals', 'wallets', 'affiliate_histories', 'transactions'];
            
            foreach ($tables as $table) {
                $file = $backupPath . '/' . $table . '.json';
                if (file_exists($file)) {
                    $data = json_decode(file_get_contents($file), true);
                    DB::table($table)->truncate();
                    
                    if (!empty($data)) {
                        foreach (array_chunk($data, 100) as $chunk) {
                            DB::table($table)->insert($chunk);
                        }
                    }
                    
                    echo "  ✓ Tabela '$table' restaurada: " . count($data) . " registros\n";
                }
            }
            
            echo "\n✅ Backup restaurado com sucesso!\n";
            
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }
}

// Executar reset
$reset = new ResetSystem();

// Verificar argumentos
if (isset($argv[1]) && $argv[1] === '--restore' && isset($argv[2])) {
    $reset->restore($argv[2]);
} else {
    $reset->execute();
}