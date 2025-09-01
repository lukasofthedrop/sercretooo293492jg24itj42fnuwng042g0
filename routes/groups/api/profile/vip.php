<?php

use App\Http\Controllers\VipController;
use Illuminate\Support\Facades\Route;

Route::prefix('vip')
    ->group(function ()
    {
        Route::get('/', [VipController::class, 'index']);
    });
