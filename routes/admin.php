<?php

use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Admin\AppVersionController;
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\CategoryController;
use App\Http\Controllers\Api\Admin\HymnCategoryController;
use App\Http\Controllers\Api\Admin\HymnController;
use App\Http\Controllers\Api\Admin\RoleController;
use App\Http\Controllers\Api\Admin\SongController;
use App\Http\Controllers\Api\Admin\SongLanguageController;
use App\Http\Controllers\Api\Admin\StyleController;
use App\Http\Controllers\Api\Admin\SuggestSongController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:admin')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/', [AuthController::class, 'detail']);

    Route::apiResource('/songs', SongController::class);
    Route::apiResource('/styles', StyleController::class);
    Route::apiResource('/categories', CategoryController::class);
    Route::apiResource('/song-languages', SongLanguageController::class);
    Route::apiResource('/hymns', HymnController::class);
    Route::apiResource('/hymn-categories', HymnCategoryController::class);
    Route::apiResource('/admins', AdminController::class);
    Route::apiResource('/app-versions', AppVersionController::class);
    Route::get('/roles', [RoleController::class, 'index']);
    Route::get('/suggest-songs', [SuggestSongController::class, 'index']);
    Route::get('/suggest-songs/{suggestSong}', [SuggestSongController::class, 'show']);
    Route::put('/suggest-songs/{suggestSong}', [SuggestSongController::class, 'update']);
    Route::post('/suggest-songs/{suggestSong}/approve', [SuggestSongController::class, 'approve']);
    Route::post('/suggest-songs/{suggestSong}/cancel', [SuggestSongController::class, 'cancel']);
});
