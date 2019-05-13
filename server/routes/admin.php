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

// ※ リクエスト数制限は全APIにかけるが、Kernel.php だと config() が使えないのでこちらで指定する
Route::middleware('throttle:' . config('app.throttle_requests') . ',1')->group(function () {
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
        Route::get('users/{user}/decks', 'DeckController@index');
        Route::get('users/{user}/quests', 'QuestController@index');
        Route::get('users/{user}/quests/logs', 'QuestController@logs');
        Route::get('users/{user}/achievements', 'AchievementController@index');
        Route::get('users/{user}/achievements/logs', 'AchievementController@logs');
        Route::get('users/{user}/gachas/logs', 'GachaController@logs');

        // 更新系APIはロールをチェック
        Route::middleware('can:writable')->group(function () {
            Route::put('users/{user}', 'UserController@update');
            Route::post('users/{user}/items', 'ItemController@store');
            Route::put('users/{user}/items/{userItem}', 'ItemController@update');
            Route::delete('users/{user}/items/{userItem}', 'ItemController@destroy');
            Route::post('users/{user}/cards', 'CardController@store');
            Route::put('users/{user}/cards/{userCard}', 'CardController@update');
            Route::delete('users/{user}/cards/{userCardId}', 'CardController@destroy');
            Route::post('users/{user}/gifts', 'GiftController@store');
            Route::delete('users/{user}/gifts/{userGift}', 'GiftController@destroy');
            Route::post('users/{user}/quests', 'QuestController@store');
            Route::delete('users/{user}/quests/{userQuest}', 'QuestController@destroy');
        });
    });
});
