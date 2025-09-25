<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Gateway\BsPayController;

Route::prefix('bspay')
    ->group(function ()
    {
        Route::any('callback', [BsPayController::class, 'callbackMethod']);
    });



