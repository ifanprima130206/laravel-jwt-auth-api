<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->name('auth.')->group(function() {

    Route::post('signup', [AuthController::class, 'signup'])->name('signup');
    Route::post('signin', [AuthController::class, 'signin'])->name('signin');
    
    Route::middleware('auth:api')->group(function () {
        Route::post('signout', [AuthController::class, 'signout'])->name('signout');
        Route::post('refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::get('me', [AuthController::class, 'me'])->name('me');
    });
});
