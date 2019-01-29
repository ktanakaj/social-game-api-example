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
// TODO: 管理者認証は未整備

// 通常のAPI
// Route::middleware('auth:admin')->group(function () {
    Route::get('users', 'UserController@index');
    Route::get('users/{id}', 'UserController@show');
    Route::get('users/{id}/items', 'UserItemController@index');
    Route::get('users/{id}/gifts', 'UserGiftController@index');
    Route::post('users/{id}/gifts', 'UserGiftController@store');
// });
