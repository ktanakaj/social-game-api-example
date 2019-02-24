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
Route::get('env', 'SystemController@env');
Route::get('masters', 'MasterController@index');
Route::get('masters/{name}', 'MasterController@findMaster');

// ユーザー登録&認証API
Route::post('users', 'UserController@store');
Route::post('login', 'AuthController@login');
Route::post('logout', 'AuthController@logout');

// 通常のAPI
Route::middleware('auth')->group(function () {
    Route::get('users/me', 'UserController@me');
    Route::get('items', 'ItemController@index');
    Route::post('items/{userItemId}/use', 'ItemController@use');
    Route::get('cards', 'CardController@index');
    Route::post('cards/{userCardId}/merge', 'CardController@merge');
    Route::delete('cards/{userCardId}', 'CardController@destroy');
    Route::get('gifts', 'GiftController@index');
    Route::post('gifts/recv', 'GiftController@receiveAll');
    Route::post('gifts/{userGiftId}/recv', 'GiftController@receive');
    Route::get('decks', 'DeckController@index');
    Route::post('decks', 'DeckController@store');
    Route::put('decks/{userDeckId}', 'DeckController@update');
    Route::delete('decks/{userDeckId}', 'DeckController@destroy');
    Route::get('achievements', 'AchievementController@index');
    Route::post('achievements/recv', 'AchievementController@receiveAll');
    Route::post('achievements/{userAchievementId}/recv', 'AchievementController@receive');
    Route::get('gachas', 'GachaController@index');
    Route::post('gachas', 'GachaController@lot');
    Route::post('game/start', 'GameController@start');
    Route::post('game/end', 'GameController@end');
});

// 開発環境用の特殊なAPI
if (is_callable('\\OpenApi\\scan')) {
    Route::get('api-docs.json', 'OpenApiController');
}
