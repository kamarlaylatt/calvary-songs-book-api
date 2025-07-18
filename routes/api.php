<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\User\SongController;

Route::prefix('admin')->group(function () {
    require __DIR__ . '/admin.php';
});

Route::post('/login', [AuthController::class, 'login']);

// Public APIs
Route::get('/songs', [SongController::class, 'index']);
Route::get('/songs/{song:slug}', [SongController::class, 'show']);
Route::get('/categories', [SongController::class, 'categories']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'me']);
});
