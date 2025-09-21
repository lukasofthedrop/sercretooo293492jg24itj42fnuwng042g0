<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Exception;

class SecureDB
{
    /**
     * Executa raw query com validação de segurança
     */
    public static function raw($query, array $bindings = [])
    {
        // Detecta tentativa de SQL injection
        $dangerous = ['--', ';', '/*', '*/', 'xp_', 'sp_', '0x', 'exec', 'execute', 'declare'];
        $queryLower = strtolower($query);
        
        foreach ($dangerous as $pattern) {
            if (strpos($queryLower, $pattern) !== false) {
                \Log::warning('Possível SQL Injection bloqueado', [
                    'query' => $query,
                    'ip' => request()->ip()
                ]);
                throw new Exception('Query suspeita bloqueada');
            }
        }
        
        // Se tem bindings, usa prepared statement
        if (!empty($bindings)) {
            return DB::raw($query);
        }
        
        // Se tem aspas sem bindings, é perigoso
        if ((strpos($query, '"') !== false || strpos($query, "'") !== false) && empty($bindings)) {
            throw new Exception('Use bindings para valores dinâmicos');
        }
        
        return DB::raw($query);
    }
}
