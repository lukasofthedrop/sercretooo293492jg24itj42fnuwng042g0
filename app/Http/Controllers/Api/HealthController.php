<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class HealthController extends Controller
{
    /**
     * Health Check endpoint para Render
     */
    public function __invoke()
    {
        try {
            // Verifica conexão com banco de dados
            DB::connection()->getPdo();
            $dbStatus = 'connected';
            $dbError = null;
        } catch (\Exception $e) {
            $dbStatus = 'disconnected';
            $dbError = $e->getMessage();
        }
        
        // Verifica cache
        try {
            Cache::put('health_check', true, 60);
            $cacheStatus = Cache::has('health_check') ? 'working' : 'not_working';
        } catch (\Exception $e) {
            $cacheStatus = 'error';
        }
        
        // Status geral
        $isHealthy = $dbStatus === 'connected' && $cacheStatus === 'working';
        
        return response()->json([
            'status' => $isHealthy ? 'healthy' : 'unhealthy',
            'timestamp' => now()->toIso8601String(),
            'version' => config('app.version', '1.0.0'),
            'environment' => app()->environment(),
            'database' => [
                'status' => $dbStatus,
                'error' => $dbError
            ],
            'cache' => [
                'status' => $cacheStatus
            ],
            'uptime' => round((microtime(true) - LARAVEL_START), 2) . ' seconds',
            'memory' => [
                'usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
                'peak' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB'
            ]
        ], $isHealthy ? 200 : 503);
    }
}
