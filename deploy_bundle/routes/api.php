<?php

use App\Http\Controllers\Api\Profile\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Games\GameController;
use App\Models\Promocao;
use App\Http\Controllers\MissionController;
use App\Http\Controllers\VipController;
use App\Http\Controllers\Api\MinesController;
use App\Http\Controllers\Api\MinesHistoryController;
use App\Http\Controllers\Api\DailyBonusController;
use App\Http\Controllers\Api\GameOpenController;
use App\Http\Controllers\Api\DashboardMetricsController;

// API Health Check
Route::get('/', function () {
    return response()->json(['message' => 'API is running']);
});

/*
|--------------------------------------------------------------------------
| ROTA DAS MISSÕES
|--------------------------------------------------------------------------

*/





/*
|--------------------------------------------------------------------------
| ROTA DAS PROMOÇÕES
|--------------------------------------------------------------------------

*/


Route::get('/promocoes', function () {
    return Promocao::all();
});

/*
|--------------------------------------------------------------------------
| API Routes 
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('jogos')->group(function () {
    // Rota para listar todos os jogos
    Route::get('/lista', [GameController::class, 'listarTodos']);

    // Rota para buscar jogos por nome
    Route::get('/procurar', [GameController::class, 'buscarPorNome']);

    // Rota para buscar jogos por categoria
    Route::get('/categorias', [GameController::class, 'buscarPorCategoria']);

    // Rota para buscar jogos por provedora
    Route::get('/provedora', [GameController::class, 'buscarPorProvedora']);
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth.jwt']], function () {
    Route::prefix('mines')->group(function () {
        Route::post('/start', [MinesController::class, 'startGame']);
        Route::post('/reveal', [MinesController::class, 'revealCell']);
        Route::post('/cashout', [MinesController::class, 'cashOut']);
        Route::post('/history', [MinesHistoryController::class, 'getHistory']);
    });

    // Rotas do Daily Bonus
    Route::prefix('daily-bonus')->group(function () {
        Route::get('/check', [DailyBonusController::class, 'check']);
        Route::post('/claim', [DailyBonusController::class, 'claim']);
    });
    Route::get('/games/open-check', [GameOpenController::class, 'checkDailyDeposit']);
    
    // Rotas de saque afiliado
    Route::prefix('affiliate')->group(function () {
        Route::post('/withdrawal/request', [\App\Http\Controllers\AffiliateWithdrawalController::class, 'requestWithdrawal']);
        Route::get('/withdrawal/history', [\App\Http\Controllers\AffiliateWithdrawalController::class, 'getWithdrawalHistory']);
    });
});
/*
 * Auth Route with JWT
 */
Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
    include_once(__DIR__ . '/groups/api/auth/auth.php');
});

// Dashboard Metrics Routes (sem JWT para admin)
Route::prefix('admin')->group(function () {
    Route::get('/dashboard-metrics', [DashboardMetricsController::class, 'index']);
    Route::get('/dashboard-metrics/sparkline/{type}', [DashboardMetricsController::class, 'sparkline']);
    Route::get('/dashboard-metrics/export', [DashboardMetricsController::class, 'export']);
    Route::get('/dashboard-metrics/test', [DashboardMetricsController::class, 'generateTestData']);
    Route::post('/dashboard-metrics/clear-cache', [DashboardMetricsController::class, 'clearCache']);
    Route::post('/dashboard-metrics/reset-system', [DashboardMetricsController::class, 'resetSystem']);
});

Route::group(['middleware' => ['auth.jwt']], function () {
    

    
    Route::prefix('missions')
        ->group(function () {
            Route::get('/', [MissionController::class, 'index']); // Lista missões
            Route::post('/{missionId}/progress', [MissionController::class, 'updateProgress']); // Atualiza progresso
            Route::post('/{missionId}/redeem', [MissionController::class, 'redeemReward']); // Resgata recompensa
            Route::get('/{missionId}/check', [MissionController::class, 'checkIfRedeemed']); // Verifica se foi resgatada
        });

    Route::prefix('vips')
        ->group(function () {
            Route::get('/', [VipController::class, 'getVipsWithProgress']); // Lista níveis VIP com progresso
            Route::post('/{vipId}/claim', [VipController::class, 'claimVipReward']); // Resgata recompensa semanal
        });
        
    Route::prefix('profile')
        ->group(function ()
        {
            include_once(__DIR__ . '/groups/api/profile/profile.php');
            include_once(__DIR__ . '/groups/api/profile/affiliates.php');
            include_once(__DIR__ . '/groups/api/profile/wallet.php');
            include_once(__DIR__ . '/groups/api/profile/likes.php');
            include_once(__DIR__ . '/groups/api/profile/favorites.php');
            include_once(__DIR__ . '/groups/api/profile/recents.php');
            include_once(__DIR__ . '/groups/api/profile/vip.php');
        });

    Route::prefix('carteira_wallet')
        ->group(function ()
        {
            include_once(__DIR__ . '/groups/api/wallet/deposit.php');
            include_once(__DIR__ . '/groups/api/wallet/withdraw.php');
        });




});


Route::prefix('categories')
    ->group(function ()
    {
        include_once(__DIR__ . '/groups/api/categories/index.php');;
    });

include_once(__DIR__ . '/groups/api/games/index.php');
// include_once(__DIR__ . '/groups/api/gateways/suitpay.php'); // REMOVIDO - APENAS AUREOLINK ATIVO

Route::prefix('pesquisar_games')
    ->group(function ()
    {
        include_once(__DIR__ . '/groups/api/search/search.php');
    });

Route::prefix('profile')
    ->group(function ()
    {
        Route::post('/getLanguage', [ProfileController::class, 'getLanguage']);
        Route::put('/updateLanguage', [ProfileController::class, 'updateLanguage']);
    });

Route::prefix('providers')
    ->group(function ()
    {

    });


Route::prefix('settings')
    ->group(function ()
    {
        include_once(__DIR__ . '/groups/api/settings/settings.php');
        include_once(__DIR__ . '/groups/api/settings/banners.php');
        include_once(__DIR__ . '/groups/api/settings/currency.php');
        include_once(__DIR__ . '/groups/api/settings/bonus.php');
    });

// LANDING SPIN

