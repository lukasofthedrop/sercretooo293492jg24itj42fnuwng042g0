<?php

use App\Http\Controllers\Gateway\EzzePayController;
use Illuminate\Support\Facades\Route;

Route::prefix('ezzepay')
->group(function ()
{
    Route::post('webhook', [EzzePayController::class, 'webhook']);
  
});


?>