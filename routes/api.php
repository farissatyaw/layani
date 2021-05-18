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
    Route::get('test', function () {
        if (auth()->user()->tokenCan('owner')) {
            return 'test';
        } else {
            return 'test2';
        }
    });
    Route::put('update', [App\Http\Controllers\UserController::class, 'update']);
    Route::delete('delete', [App\Http\Controllers\UserController::class, 'destroy']);
});
