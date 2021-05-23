<?php

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

Route::post('login', [App\Http\Controllers\UserController::class, 'doLogin']);
Route::post('logout', [App\Http\Controllers\UserController::class, 'doLogout']);
Route::post('register', [App\Http\Controllers\UserController::class, 'create']);
Route::group(['middleware' => 'auth:sanctum'], function () {
    //Debugging Purpose Only
    Route::get('test', function () {
        if (auth()->user()->tokenCan('admin')) {
            return 'test';
        } else {
            return 'test2';
        }
    });
    Route::prefix('user')->group(function () {
        Route::get('/', [App\Http\Controllers\UserController::class, 'show']);
    });
    Route::get('leaderboard', [App\Http\Controllers\UserController::class, 'leaderboard']);
    Route::put('update', [App\Http\Controllers\UserController::class, 'update']);
    Route::delete('delete', [App\Http\Controllers\UserController::class, 'destroy']);

    Route::prefix('complaint')->group(function () {
        Route::get('index', [App\Http\Controllers\ComplaintController::class, 'index']);
        Route::post('create', [App\Http\Controllers\ComplaintController::class, 'create']);
        Route::post('/{complaint}/accept', [App\Http\Controllers\ComplaintController::class, 'acceptComplaint']);
        Route::post('/{complaint}/finish', [App\Http\Controllers\ComplaintController::class, 'finishComplaint']);
    });
});
Route::get('sukses', function () {
    return 'sukses';
});
