<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('login/google', [GoogleAuthController::class, 'redirectToGoogle']);
Route::get('callback/google', [GoogleAuthController::class, 'handleGoogleCallback']);
