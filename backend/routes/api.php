<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\TodoController;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/todos', [TodoController::class , 'index']);

//thêm todos
Route::post('/todos/store', [TodoController::class, 'store']);