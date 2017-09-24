<?php

use Illuminate\Http\Request;

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

//TODO: 未認証・ユーザー認証・管理者認証の3パターンを想定

Route::get('users', 'UserController@index');
Route::get('users/{id}', 'UserController@show');
Route::post('users', 'UserController@store');

Route::get('users/{id}/items', 'UserItemController@index');

Route::get('users/{id}/gifts', 'UserGiftController@index');
Route::post('users/{id}/gifts', 'UserGiftController@store');
Route::post('users/me/gifts/{userGiftId}/recv', 'UserGiftController@receive');

Route::get('masters/events', 'MasterController@getEvents');
Route::get('masters/gift_messages', 'MasterController@getGiftMessages');
Route::get('masters/items', 'MasterController@getItems');
Route::get('masters/item_properties', 'MasterController@getItemProperties');
Route::get('masters/news', 'MasterController@getNews');
