<?php

use App\Http\Controllers\Api\Games\GameController;
use App\Http\Controllers\Api\Profile\WalletController;
use App\Models\Game;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\LicenseKeyController;
// Removed alias to avoid duplicate import conflicts in includes
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('layouts.app');
})->name('home');

Route::get('/storage/{path}', function (string $path) {
    $disk = Storage::disk('public');

    if (! $disk->exists($path)) {
        abort(404);
    }

    $targetUrl = $disk->url($path);

    return redirect()->away($targetUrl, 302);
})->where('path', '.*');

/*
|--------------------------------------------------------------------------
| Web LIMPAR CACHE
|--------------------------------------------------------------------------
*/


Route::get('/clear', function () {
    // Limpa todas as caches principais
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return back()->with('status', 'Cache limpo com sucesso!');
})->name('clear.cache');

Route::get('/update-colors', function () {
    // Limpa qualquer output anterior para evitar problemas com JSON
    if (ob_get_level()) ob_end_clean();
    ob_start();
    
    // REVERSÃO SEGURA: Mantém comandos Artisan necessários 
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    // Remove comandos mais agressivos que causam restart
    
    // Se for uma requisição AJAX, retorna JSON
    if (request()->ajax() || request()->wantsJson()) {
        ob_clean(); // Limpa qualquer saída antes do JSON
        return response()->json([
            'success' => true,
            'message' => '🎨 Cores atualizadas com sucesso!'
        ]);
    }
    
    return back()->with('status', '🎨 Cores atualizadas (restart mínimo)!');
})->name('update.colors');

Route::get('/clear-memory', function () {
    // Limpa todas as caches e remove arquivos temporários de compilação
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return back()->with('status', 'Memória limpa com sucesso!');
})->name('clear.memory');

Route::get('/optimize-system', function () {
    // Executa otimização do sistema
    Artisan::call('optimize');
    return back()->with('status', 'Sistema otimizado com sucesso!');
})->name('optimize.system');


/*
|--------------------------------------------------------------------------
*/


// SISTEMA DE DISTRIBUIÇAO


Route::get('api/distribution/manual-update', [DistributionController::class, 'checkDistributionSystem'])
    ->name('distribution.manual.update');

/*
|--------------------------------------------------------------------------
*/




// GAMES PROVIDER

include_once(__DIR__ . '/groups/provider/playFiver.php');
include_once(__DIR__ . '/groups/ControlPainel/calbackmetod.php');

// GATEWAYS - REMOVIDOS (APENAS AUREOLINK ATIVO)
// include_once(__DIR__ . '/groups/gateways/bspay.php');
// include_once(__DIR__ . '/groups/gateways/stripe.php');
// include_once(__DIR__ . '/groups/gateways/ezzepay.php');
// include_once(__DIR__ . '/groups/gateways/digitopay.php');

// AUREOLINK GATEWAY
Route::prefix('aureolink')->group(function () {
    Route::post('/webhook', [App\Http\Controllers\AureoLinkController::class, 'webhook'])->name('aureolink.webhook');
    Route::post('/deposit', [App\Http\Controllers\AureoLinkController::class, 'createDeposit'])->name('aureolink.deposit');
    Route::post('/withdrawal', [App\Http\Controllers\AureoLinkController::class, 'createWithdrawal'])->name('aureolink.withdrawal');
    Route::get('/transaction/{id}/status', [App\Http\Controllers\AureoLinkController::class, 'checkTransactionStatus'])->name('aureolink.transaction.status');
});

/// SOCIAL
/// include_once(__DIR__ . '/groups/auth/social.php');

// 2FA ROUTES
include_once(__DIR__ . '/2fa.php');

// LOGIN ROUTE FOR AUTHENTICATION
// Redirects to the proper panel login based on context (affiliate vs admin)
Route::get('/login', function (Request $request) {
    $panel = $request->query('panel');
    $referer = (string) $request->headers->get('referer', '');
    $path = ltrim($request->path(), '/');

    // Explicit query param takes priority
    if ($panel === 'affiliate' || str_contains($referer, '/afiliado') || str_starts_with($path, 'afiliado')) {
        // Prefer URL limpa /afiliado (Nginx reescreve internamente para /afiliado/login)
        return redirect('/afiliado');
    }

    // Prefer URL limpa /admin (Nginx reescreve internamente para /admin/login)
    return redirect('/admin');
})->name('login');

// SISTEMA DE LOGOUT COMPLETO
Route::get('/logout-completo', [App\Http\Controllers\LogoutController::class, 'logout'])->name('logout.completo');
Route::get('/escolher-painel', [App\Http\Controllers\LogoutController::class, 'escolherPainel'])->name('escolher.painel');

// DASHBOARD DO AFILIADO - ANTES DO CATCH-ALL
// Se USE_CUSTOM_AFFILIATE_PANEL=true no .env, usa o painel custom.
// Caso contrário, deixa o Filament (panel 'afiliado') atender /afiliado.

if (env('USE_CUSTOM_AFFILIATE_PANEL', false)) {
    Route::get('/afiliado', [\App\Http\Controllers\Api\Profile\AffiliateController::class, 'painelAfiliado'])->name('afiliado');

    Route::middleware(['auth'])->group(function () {
        // Direciona também o dashboard para a versão customizada
        Route::get('/affiliate/dashboard', [\App\Http\Controllers\Api\Profile\AffiliateController::class, 'painelAfiliado'])->name('affiliate.dashboard');
    });
}

// Exibir login do painel diretamente em /admin sem mudar a URL
Route::get('/admin', function () {
    $sub = \Illuminate\Http\Request::create('/admin/login', 'GET');
    $response = app()->handle($sub);
    return response($response->getContent(), 200)
        ->withHeaders($response->headers->all());
})->name('admin.entry');

// Exibir login do painel de afiliado diretamente em /afiliado (URL limpa) quando não autenticado.
// Se autenticado, envia para a dashboard do painel afiliado.
Route::get('/afiliado', function () {
    if (! auth()->check()) {
        $sub = \Illuminate\Http\Request::create('/afiliado/login', 'GET');
        $response = app()->handle($sub);
        return response($response->getContent(), 200)
            ->withHeaders($response->headers->all());
    }

    return redirect('/afiliado/minha-dashboard');
})->name('afiliado.entry');

// /afiliado é tratado no Nginx para exibir o login sem alterar a URL

// Teste simples
Route::get('/teste-afiliado', function() {
    return "Dashboard do Afiliado funcionando!";
});

// Teste temporário sem auth
Route::get('/painel-afiliado-demo', function() {
    $user = \App\Models\User::find(15); // Admin user
    
    if (!$user) {
        return "Usuário não encontrado";
    }
    
    $settings = \App\Models\AffiliateSettings::getOrCreateForUser($user->id);
    
    // Dados simulados para demonstração
    $monthlyData = [
        ['month' => 'Abr/2025', 'ngr' => 5000, 'commission' => 2000],
        ['month' => 'Mai/2025', 'ngr' => 7500, 'commission' => 3000],
        ['month' => 'Jun/2025', 'ngr' => 10000, 'commission' => 4000],
        ['month' => 'Jul/2025', 'ngr' => 12500, 'commission' => 5000],
        ['month' => 'Ago/2025', 'ngr' => 15000, 'commission' => 6000],
        ['month' => 'Set/2025', 'ngr' => 8000, 'commission' => 3200],
    ];
    
    $recentReferred = [
        [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'created_at' => '01/09/2025',
            'is_active' => true,
            'total_deposited' => 500,
            'commission_generated' => 200
        ],
        [
            'name' => 'Maria Santos',
            'email' => 'maria@example.com',
            'created_at' => '28/08/2025',
            'is_active' => true,
            'total_deposited' => 750,
            'commission_generated' => 300
        ],
        [
            'name' => 'Pedro Costa',
            'email' => 'pedro@example.com',
            'created_at' => '15/08/2025',
            'is_active' => false,
            'total_deposited' => 250,
            'commission_generated' => 100
        ]
    ];
    
    return view('affiliate.painel-dashboard', [
        'user' => $user,
        'affiliate_code' => $user->inviter_code ?? 'AFF2025DEMO',
        'invite_link' => url('/register?code=' . ($user->inviter_code ?? 'AFF2025DEMO')),
        'total_referred' => 12,
        'active_referred' => 8,
        'available_balance' => 3200.50,
        'total_earned' => 15750.00,
        'month_ngr' => 8000,
        'revshare_percentage' => 40,
        'monthly_data' => $monthlyData,
        'recent_referred' => $recentReferred,
        'settings' => $settings
    ]);
});

// APP - CATCH-ALL ROUTE - DEVE SER A ÚLTIMA!
include_once(__DIR__ . '/groups/layouts/app.php');

// Rota web para abrir jogo por ID ou game_code
Route::get('/game/{key}', [\App\Http\Controllers\GameWebController::class, 'show'])->name('game.launch');

// Rotas LICENSE API
// Route::prefix('admin/license')->middleware(['web'])->group(function () {
//     Route::get('/', [LicenseKeyController::class, 'index'])->name('license.index');
//     Route::post('/', [LicenseKeyController::class, 'store'])->name('license.store');
//     Route::put('/{id}', [LicenseKeyController::class, 'update'])->name('license.update');
//     Route::delete('/{id}', [LicenseKeyController::class, 'destroy'])->name('license.destroy');
// });

// AureoLink Gateway Routes (definido uma vez)
Route::prefix('aureolink')->group(function () {
    Route::post('/webhook', [App\Http\Controllers\AureoLinkController::class, 'webhook'])->name('aureolink.webhook');
    Route::post('/deposit', [App\Http\Controllers\AureoLinkController::class, 'createDeposit'])->name('aureolink.deposit');
    Route::post('/withdrawal', [App\Http\Controllers\AureoLinkController::class, 'createWithdrawal'])->name('aureolink.withdrawal');
    Route::get('/transaction/{id}/status', [App\Http\Controllers\AureoLinkController::class, 'checkTransactionStatus'])->name('aureolink.transaction.status');
});
