<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class AutoBackup extends Command
{
    protected $signature = 'backup:auto';
    protected $description = 'Backup automático do sistema';

    public function handle()
    {
        $date = Carbon::now()->format('Y-m-d-H-i-s');
        $backupPath = storage_path("backups/backup-{$date}");
        
        // Criar diretório
        if (!file_exists(storage_path('backups'))) {
            mkdir(storage_path('backups'), 0755, true);
        }
        
        // Backup do banco
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        
        $command = "mysqldump -u {$username}";
        if ($password) {
            $command .= " -p{$password}";
        }
        $command .= " {$database} > {$backupPath}.sql";
        
        exec($command);
        
        // Compactar
        exec("tar -czf {$backupPath}.tar.gz {$backupPath}.sql");
        unlink("{$backupPath}.sql");
        
        // Deletar backups antigos (manter últimos 30)
        $this->deleteOldBackups();
        
        $this->info("Backup criado: {$backupPath}.tar.gz");
    }
    
    private function deleteOldBackups()
    {
        $files = glob(storage_path('backups/*.tar.gz'));
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        // Manter apenas os 30 mais recentes
        $toDelete = array_slice($files, 30);
        foreach ($toDelete as $file) {
            unlink($file);
        }
    }
}
