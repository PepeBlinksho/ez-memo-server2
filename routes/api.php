<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\MemoController;
use App\Http\Controllers\API\V1\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->namespace('App\Http\Controllers\API\V1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [UserController::class, 'register']);
    });

    Route::prefix('memos')->group(function () {
        Route::get('/', [MemoController::class, 'view']);
        Route::post('/', [MemoController::class, 'store']);
        Route::patch('/{id}', [MemoController::class, 'update']);
        Route::delete('/{id}', [MemoController::class, 'delete']);
    });

    Route::prefix('user')->middleware('auth:api')->group(function () {
        Route::prefix('memos')->group(function () {
            Route::get('/', [MemoController::class, 'view']);
            Route::post('/', [MemoController::class, 'store']);
            Route::patch('/{id}', [MemoController::class, 'update']);
            Route::delete('/{id}', [MemoController::class, 'delete']);
        });
    });
});