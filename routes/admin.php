<?php

use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\SongController;
use App\Http\Controllers\Api\Admin\SongSuggestionController;
use App\Http\Controllers\Api\Admin\StyleController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\SongLanguageController;
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Admin\RoleController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:admin')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/', [AuthController::class, 'detail']);

    Route::apiResource('/songs', SongController::class);
    Route::apiResource('/styles', StyleController::class);
    Route::apiResource('/categories', CategoryController::class);
    Route::apiResource('/song-languages', SongLanguageController::class);
    Route::apiResource('/admins', AdminController::class);
    Route::get('/roles', [RoleController::class, 'index']);
    
    // Song Suggestions
    Route::apiResource('/song-suggestions', SongSuggestionController::class);
    Route::post('/song-suggestions/{songSuggestion}/approve', [SongSuggestionController::class, 'approve']);
    Route::post('/song-suggestions/{songSuggestion}/cancel', [SongSuggestionController::class, 'cancel']);
});
