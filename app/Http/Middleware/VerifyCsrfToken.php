<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/*',
        'suitpay/*',
        'vgames/*',
        'webhooks/*',
        'cron/*',
        'quality/*',
        'playfiver/*',
        'ezzepay/*',
        'digitopay/*',
        'bspay/*',
        'pixupbr/*',
        '/cores'  // Excluindo a rota /cores da verificação CSRF
    ];
}
