<?php

return [
    'alerts' => [
        'failed_logins' => 5,        // Alerta após 5 falhas
        'sql_injection_attempts' => 1, // Alerta imediato
        'xss_attempts' => 3,          // Alerta após 3 tentativas
        'high_memory_usage' => 80,    // Alerta em 80% de memória
        'high_cpu_usage' => 70,       // Alerta em 70% de CPU
    ],
    
    'notifications' => [
        'email' => env('SECURITY_ALERT_EMAIL'),
        'slack' => env('SECURITY_SLACK_WEBHOOK'),
    ],
    
    'auto_block' => [
        'enabled' => true,
        'threshold' => 10,  // Bloqueia IP após 10 tentativas suspeitas
        'duration' => 3600, // Bloqueia por 1 hora
    ]
];
