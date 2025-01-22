<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\State\GameStateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('v1')->group(function () {
    Route::prefix('user')->group(function () {
        Route::post('auth/tg/callback', [LoginController::class, 'login']);
        Route::post('auth', [LoginController::class, 'login']);
        Route::post('registration', [RegistrationController::class, 'registration']);
    });
});

Route::prefix('v1')->group(function () {
    Route::prefix('stat')->group(function () {
        Route::get('game', [GameStateController::class, 'index']);
    });
});




