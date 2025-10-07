<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TokenController;
use App\Http\Controllers\Api\MeController;
use App\Http\Controllers\Api\DatingProfileController;
use App\Http\Controllers\Api\ProfileImageController;
use App\Http\Controllers\Api\ProfileImageShareController;

Route::prefix('v1')->group(function () {

    Route::post('/auth/token', [TokenController::class, 'store'])->name('api.v1.auth.token.store');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [MeController::class, 'index'])->name('api.v1.me.index');
        Route::get('/me/show', [MeController::class, 'show'])->name('api.v1.me.show');

        Route::get('/auth/tokens', [TokenController::class, 'index'])->name('api.v1.auth.tokens.index');
        Route::get('/auth/tokens/{id}', [TokenController::class, 'show'])->name('api.v1.auth.tokens.show');
        Route::put('/auth/tokens/{id}', [TokenController::class, 'update'])->name('api.v1.auth.tokens.update');

        Route::delete('/auth/token', [TokenController::class, 'destroy'])->name('api.v1.auth.token.destroy');
     
        Route::apiResource('dating-profiles', DatingProfileController::class)->only(['index', 'store', 'show', 'update', 'destroy']);
    
        Route::apiResource('profile-images', ProfileImageController::class);
        Route::apiResource('profile-images', ProfileImageController::class);

    // ÚJ: megosztások CRUD
    Route::apiResource('profile-image-shares', ProfileImageShareController::class);
    
    
    });
});
