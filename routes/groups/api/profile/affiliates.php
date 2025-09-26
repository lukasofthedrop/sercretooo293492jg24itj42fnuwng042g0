<?php

use App\Http\Controllers\Api\Profile\AffiliateController;
use Illuminate\Support\Facades\Route;

Route::prefix('affiliates')
    ->group(function ()
    {
        Route::get('/', [AffiliateController::class, 'index']);
        Route::get('/generate', [AffiliateController::class, 'generateCode']);
        Route::get('/metrics', [AffiliateController::class, 'getMetrics']);
        Route::post('/request', [AffiliateController::class, 'makeRequest']);
    });
