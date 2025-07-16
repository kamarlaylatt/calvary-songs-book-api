<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\AuthController;

Route::prefix('admin')->group(function () {
    require __DIR__ . '/admin.php';
});

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'me']);
});
