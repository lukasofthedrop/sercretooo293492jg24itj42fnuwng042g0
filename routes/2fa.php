<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TwoFactorController;

/*
|--------------------------------------------------------------------------
| Two Factor Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('2fa')->name('2fa.')->group(function () {
    // Setup 2FA
    Route::get('/setup', [TwoFactorController::class, 'show'])->name('setup');
    Route::post('/enable', [TwoFactorController::class, 'enable'])->name('enable');
    Route::post('/disable', [TwoFactorController::class, 'disable'])->name('disable');
    
    // Verify 2FA
    Route::get('/verify', [TwoFactorController::class, 'showVerify'])->name('verify');
    Route::post('/verify', [TwoFactorController::class, 'verify'])->name('verify.submit');
});