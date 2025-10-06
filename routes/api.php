<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TokenController;
use App\Http\Controllers\Api\MeController;

Route::prefix('v1')->group(function () {

    Route::post('/auth/token', [TokenController::class, 'store'])->name('api.v1.auth.token.store');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [MeController::class, 'index'])->name('api.v1.me.index');
        Route::get('/me/show', [MeController::class, 'show'])->name('api.v1.me.show');

        Route::get('/auth/tokens', [TokenController::class, 'index'])->name('api.v1.auth.tokens.index');
        Route::get('/auth/tokens/{id}', [TokenController::class, 'show'])->name('api.v1.auth.tokens.show');
        Route::put('/auth/tokens/{id}', [TokenController::class, 'update'])->name('api.v1.auth.tokens.update');

        Route::delete('/auth/token', [TokenController::class, 'destroy'])->name('api.v1.auth.token.destroy');
    });
});
