<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('signup', [AuthController::class, 'signup']);
Route::post('signin', [AuthController::class, 'signin']);

Route::middleware('auth:api')->group(function () {
    Route::prefix('/v1')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('books', BookController::class);
    });
});
