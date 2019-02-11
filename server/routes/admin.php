<?php

/*
|--------------------------------------------------------------------------
| Administrator API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "admin" middleware group. Enjoy building your API!
|
*/

// 認証API
Route::post('login', 'AuthController@login');
Route::post('logout', 'AuthController@logout');

// 通常のAPI
Route::middleware('auth:admin')->group(function () {
    Route::get('administrators/me', 'AdministratorController@me');
    Route::get('users', 'UserController@index');
    Route::get('users/{user}', 'UserController@show');
    Route::get('users/{user}/items', 'ItemController@index');
    Route::get('users/{user}/cards', 'CardController@index');
    Route::get('users/{user}/gifts', 'GiftController@index');
    Route::post('users/{user}/gifts', 'GiftController@store');
});
