<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class MonitoringService
{
    /**
     * Monitora métricas do sistema
     */
    public static function checkSystemHealth()
    {
        $metrics = [
            'cpu_usage' => self::getCpuUsage(),
            'memory_usage' => self::getMemoryUsage(),
            'disk_usage' => self::getDiskUsage(),
            'database_connections' => self::getDatabaseConnections(),
            'redis_memory' => self::getRedisMemory(),
            'response_time' => self::getAverageResponseTime(),
            'error_rate' => self::getErrorRate(),
            'active_users' => self::getActiveUsers(),
        ];
        
        // Verificar limiares e enviar alertas
        foreach ($metrics as $metric => $value) {
            self::checkThreshold($metric, $value);
        }
        
        // Salvar métricas para dashboard
        Cache::put('system_metrics', $metrics, 60);
        
        return $metrics;
    }
    
    private static function getCpuUsage()
    {
        $load = sys_getloadavg();
        $cores = shell_exec("grep -c ^processor /proc/cpuinfo");
        return round(($load[0] / $cores) * 100, 2);
    }
    
    private static function getMemoryUsage()
    {
        $free = shell_exec('free -b');
        $free = (string)trim($free);
        $free_arr = explode("\n", $free);
        $mem = explode(" ", $free_arr[1]);
        $mem = array_filter($mem);
        $mem = array_values($mem);
        
        $memory_usage = round(($mem[2] / $mem[1]) * 100, 2);
        return $memory_usage;
    }
    
    private static function getDiskUsage()
    {
        $disk_free = disk_free_space("/");
        $disk_total = disk_total_space("/");
        return round((($disk_total - $disk_free) / $disk_total) * 100, 2);
    }
    
    private static function getDatabaseConnections()
    {
        return DB::select("SHOW STATUS WHERE Variable_name = 'Threads_connected'")[0]->Value;
    }
    
    private static function getRedisMemory()
    {
        try {
            $info = \Redis::info('memory');
            return round($info['used_memory'] / 1024 / 1024, 2); // MB
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    private static function getAverageResponseTime()
    {
        return Cache::get('avg_response_time', 0);
    }
    
    private static function getErrorRate()
    {
        $total = Cache::get('total_requests', 1);
        $errors = Cache::get('error_requests', 0);
        return round(($errors / $total) * 100, 2);
    }
    
    private static function getActiveUsers()
    {
        return Cache::get('active_users', 0);
    }
    
    private static function checkThreshold($metric, $value)
    {
        $thresholds = [
            'cpu_usage' => 80,
            'memory_usage' => 85,
            'disk_usage' => 90,
            'database_connections' => 100,
            'redis_memory' => 1024, // 1GB
            'response_time' => 1000, // 1s
            'error_rate' => 5, // 5%
        ];
        
        if (isset($thresholds[$metric]) && $value > $thresholds[$metric]) {
            Log::critical("System Alert: {$metric} is at {$value} (threshold: {$thresholds[$metric]})");
            // Aqui você pode adicionar notificação por email/Slack
        }
    }
}
