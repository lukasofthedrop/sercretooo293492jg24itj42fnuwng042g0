<?php

use App\Http\Controllers\Api\Games\GameController;
use App\Http\Controllers\Api\Profile\WalletController;
use App\Models\Game;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\DistributionController;
use App\Http\Controllers\LicenseKeyController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------



Route::get('loadinggame', function () {});
Route::get('/withdrawal/{id}', [WalletController::class, 'withdrawalFromModal'])->name('withdrawal');
*/
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

// GATEWAYS
include_once(__DIR__ . '/groups/gateways/bspay.php');
include_once(__DIR__ . '/groups/gateways/stripe.php');
include_once(__DIR__ . '/groups/gateways/ezzepay.php');
include_once(__DIR__ . '/groups/gateways/digitopay.php');

// AUREOLINK GATEWAY
Route::prefix('aureolink')->group(function () {
    Route::post('/webhook', [App\Http\Controllers\AureoLinkController::class, 'webhook'])->name('aureolink.webhook');
    Route::post('/deposit', [App\Http\Controllers\AureoLinkController::class, 'createDeposit'])->name('aureolink.deposit');
    Route::post('/withdrawal', [App\Http\Controllers\AureoLinkController::class, 'createWithdrawal'])->name('aureolink.withdrawal');
    Route::get('/transaction/{id}/status', [App\Http\Controllers\AureoLinkController::class, 'checkTransactionStatus'])->name('aureolink.transaction.status');
});

/// SOCIAL
/// include_once(__DIR__ . '/groups/auth/social.php');

// LOGIN ROUTE FOR AUTHENTICATION
Route::get('/login', function() {
    return redirect('/admin/login');
})->name('login');

// APP
include_once(__DIR__ . '/groups/layouts/app.php');

// Rotas LICENSE API
// Route::prefix('admin/license')->middleware(['web'])->group(function () {
//     Route::get('/', [LicenseKeyController::class, 'index'])->name('license.index');
//     Route::post('/', [LicenseKeyController::class, 'store'])->name('license.store');
//     Route::put('/{id}', [LicenseKeyController::class, 'update'])->name('license.update');
//     Route::delete('/{id}', [LicenseKeyController::class, 'destroy'])->name('license.destroy');
// });

// AureoLink Gateway Routes
Route::prefix('aureolink')->group(function () {
    Route::post('/webhook', [App\Http\Controllers\AureoLinkController::class, 'webhook'])->name('aureolink.webhook');
    Route::post('/deposit', [App\Http\Controllers\AureoLinkController::class, 'createDeposit'])->name('aureolink.deposit');
    Route::post('/withdrawal', [App\Http\Controllers\AureoLinkController::class, 'createWithdrawal'])->name('aureolink.withdrawal');
    Route::get('/transaction/{id}/status', [App\Http\Controllers\AureoLinkController::class, 'checkTransactionStatus'])->name('aureolink.transaction.status');
});

// AureoLink Gateway Routes
Route::prefix('aureolink')->group(function () {
    Route::post('/webhook', [App\Http\Controllers\AureoLinkController::class, 'webhook'])->name('aureolink.webhook');
    Route::post('/deposit', [App\Http\Controllers\AureoLinkController::class, 'createDeposit'])->name('aureolink.deposit');
    Route::post('/withdrawal', [App\Http\Controllers\AureoLinkController::class, 'createWithdrawal'])->name('aureolink.withdrawal');
    Route::get('/transaction/{id}/status', [App\Http\Controllers\AureoLinkController::class, 'checkTransactionStatus'])->name('aureolink.transaction.status');
});
