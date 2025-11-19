<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;



// Route::middleware('web')->group(function () {
//     Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);
//     Route::post('/login', [AuthController::class, 'login']);
//     Route::post('/register', [AuthController::class, 'register']);
// });
