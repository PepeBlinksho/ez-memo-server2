<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
        Route::post('register', 'UserController@register');
    });

    Route::prefix('memos')->group(function () {
        Route::get('/', 'MemoController@view');
        Route::get('/list', 'MemoController@memos');
        Route::post('/', 'MemoController@store');
        Route::patch('/{id}', 'MemoController@update');
        Route::delete('/{id}', 'MemoController@delete');
    });

    Route::prefix('user')->middleware('auth:api')->group(function () {
        Route::prefix('memos')->group(function () {
            Route::get('/', 'MemoController@view');
            Route::get('/list', 'MemoController@memos');
            Route::post('/', 'MemoController@store');
            Route::patch('/{id}', 'MemoController@update');
            Route::delete('/{id}', 'MemoController@delete');
        });
        Route::prefix('folders')->group(function () {
            Route::get('/', 'FolderController@folders');
            Route::post('/', 'FolderController@store');
            Route::patch('/{folder}', 'FolderController@update');
            Route::delete('/{folder}', 'FolderController@delete');
        });
    });
});