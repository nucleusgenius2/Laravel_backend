<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\Auth\Social\GoogleAuthController;
use App\Http\Controllers\Auth\Social\SteamAuthController;
use App\Http\Controllers\Auth\Social\TelegramAuthController;
use App\Http\Controllers\Auth\Social\VkAuthController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\State\FiatCoinController;
use App\Http\Controllers\State\GameStateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('v1')->group(function () {
    Route::prefix('user')->group(function () {
        Route::middleware(['web'])->group(function () {
            Route::get('auth/tg',[TelegramAuthController::class, 'redirect']);
            Route::get('auth/tg/callback', [TelegramAuthController::class, 'handleCallback']);
            Route::get('auth/vk', [VkAuthController::class, 'redirect']);
            Route::get('auth/vk/callback', [VkAuthController::class, 'handleCallback']);
            Route::get('auth/google', [GoogleAuthController::class, 'redirect']);
            Route::get('auth/google/callback', [GoogleAuthController::class, 'handleCallback']);
            Route::get('auth/steam', [SteamAuthController::class, 'redirect']);
            Route::get('auth/steam/callback', [SteamAuthController::class, 'handleCallback']);
        });

        Route::middleware(['auth:sanctum'])->group(function () {
            Route::get('balance', [BalanceController::class, 'index']);
            Route::post('balance', [BalanceController::class, 'store']);
            Route::post('default_balance', [BalanceController::class, 'setDefault']);
        });

        Route::post('auth', [LoginController::class, 'login']);
        Route::post('registration', [RegistrationController::class, 'registration']);
    });


    Route::prefix('stat')->group(function () {
        Route::get('currencies', [FiatCoinController::class, 'index']);
        Route::get('currencies/{code}', [FiatCoinController::class, 'show']);
        Route::get('game', [GameStateController::class, 'index']);
    });
});








