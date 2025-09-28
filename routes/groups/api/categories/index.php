<?php

use App\Http\Controllers\Api\Categories\CategoryController;
use Illuminate\Support\Facades\Route;

// Named route to satisfy frontend route helpers (Ziggy/route())
Route::get('/', [CategoryController::class, 'index'])->name('api.categories');
