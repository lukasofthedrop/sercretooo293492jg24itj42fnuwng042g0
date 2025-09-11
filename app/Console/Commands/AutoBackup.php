<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
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
        
        // Backup do banco usando Process seguro
        $database = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');
        $host = env('DB_HOST', '127.0.0.1');
        $port = env('DB_PORT', 3306);
        
        // Usar array para evitar command injection
        $dumpCommand = [
            'mysqldump',
            '-h', $host,
            '-P', $port,
            '-u', $username
        ];
        
        if ($password) {
            $dumpCommand[] = '-p' . $password;
        }
        
        $dumpCommand[] = $database;
        
        // Executar dump de forma segura
        $process = new Process($dumpCommand);
        $process->setTimeout(300); // 5 minutos timeout
        
        try {
            $process->run();
            
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
            
            // Salvar output em arquivo usando Storage
            Storage::disk('local')->put(str_replace(storage_path('app/'), '', "{$backupPath}.sql"), $process->getOutput());
            
            // Compactar usando ZipArchive (mais seguro)
            $zip = new \ZipArchive();
            if ($zip->open("{$backupPath}.zip", \ZipArchive::CREATE) === TRUE) {
                $zip->addFile("{$backupPath}.sql", basename("{$backupPath}.sql"));
                $zip->close();
                unlink("{$backupPath}.sql");
            }
        } catch (ProcessFailedException $exception) {
            $this->error('Backup falhou: ' . $exception->getMessage());
            return 1;
        }
        
        // Deletar backups antigos (manter últimos 30)
        $this->deleteOldBackups();
        
        $this->info("Backup criado: {$backupPath}.zip");
    }
    
    private function deleteOldBackups()
    {
        $files = glob(storage_path('backups/*.zip'));
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
