<?php

use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\User\HymnController;
use App\Http\Controllers\Api\User\SongController;
use App\Http\Controllers\Api\User\SuggestSongController;
use App\Http\Controllers\Api\User\VersionController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function () {
    require __DIR__.'/admin.php';
});

Route::post('/login', [AuthController::class, 'login']);

// Public APIs
// Route::get('/songs', [SongController::class, 'index']);
Route::get('/songs/{song:slug}', [SongController::class, 'show']);
Route::get('/categories', [SongController::class, 'categories']);
Route::get('/search-filters', [SongController::class, 'searchFilters']);

// Hymn APIs for mobile (Myanmar hymns with related songs)
Route::get('/hymns', [HymnController::class, 'index']);
Route::get('/hymns/{id}', [HymnController::class, 'show']);
Route::get('/hymn-categories', [HymnController::class, 'hymnCategories']);
Route::get('/hymn-filters', [HymnController::class, 'searchFilters']);
// Suggest songs API
Route::post('/suggest-songs', [SuggestSongController::class, 'store']);

// Version check endpoint
Route::post('/check-version', [VersionController::class, 'checkForceUpdate']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'me']);
});
