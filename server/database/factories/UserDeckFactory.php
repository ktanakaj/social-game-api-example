<?php

use Faker\Generator as Faker;
use App\Models\Globals\UserCard;
use App\Models\Globals\UserDeck;

$factory->define(UserDeck::class, function (Faker $faker) {
    // ※ テーブル定義上、user_idは別途外部から渡してやる必要あり
    // ※ no はユニークキーなので2件目以降は要上書き
    return [
        'no' => 1,
    ];
});

$factory->afterCreating(UserDeck::class, function (UserDeck $userDeck, Faker $faker) {
    // カードを登録してデッキに割り当て
    $user = $userDeck->user;
    $userCards = [
        factory(UserCard::class)->states('card1')->make(),
        factory(UserCard::class)->states('card2')->make(),
    ];
    $user->cards()->saveMany($userCards);

    foreach ($userCards as $i => $userCard) {
        $userDeck->cards()->create(['user_card_id' => $userCard->id, 'position' => $i + 1]);
    }
});
