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

// 公開API
Route::get('masters/events', 'MasterController@getEvents');
Route::get('masters/gift-messages', 'MasterController@getGiftMessages');
Route::get('masters/items', 'MasterController@getItems');
Route::get('masters/item-properties', 'MasterController@getItemProperties');
Route::get('masters/news', 'MasterController@getNews');

// ユーザー登録&認証API
Route::post('users', 'UserController@store');
Route::post('login', 'AuthController@login');
Route::post('logout', 'AuthController@logout');

// 通常のAPI
Route::middleware('auth')->group(function () {
    Route::get('users/me', 'UserController@me');
    Route::post('gifts/recv', 'UserGiftController@allReceive');
    Route::post('gifts/{userGiftId}/recv', 'UserGiftController@receive');
});

// 開発環境用の特殊なAPI
if (is_callable('\\OpenApi\\scan')) {
    Route::get('api-docs.json', 'OpenApiController');
}
