<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Posnet\{PosnetController};


Route::prefix('v1')->group(function () {

    Route::prefix('posnet')->group(function () {

        Route::post('/addCard', [PosnetController::class, 'addCard']); //ADD CARD

        Route::post('/payment', [PosnetController::class, 'doPayment']); //DO PAYMENT


    });

});



