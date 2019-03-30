<?php

use Faker\Generator as Faker;
use Carbon\Carbon;
use App\Models\Globals\User;
use App\Models\Globals\UserCard;
use App\Models\Globals\UserDeck;
use App\Models\Globals\UserGift;
use App\Models\Globals\UserItem;
use App\Models\Globals\UserQuest;

$factory->define(User::class, function (Faker $faker) {
    return [
        'token' => str_random(10),
        'name' => $faker->name,
        'game_coins' => $faker->numberBetween(1000, 1000000),
        'special_coins' => $faker->numberBetween(0, 1000),
        'paid_special_coins' => $faker->numberBetween(10, 10000),
        'exp' => $faker->numberBetween(100, 1000000),
        'stamina' => $faker->numberBetween(10, 100),
        // ※ nowは変更されている可能性があるのでCarbonから取る
        'last_login' => $faker->dateTime(Carbon::now()),
    ];
});

$factory->afterCreating(User::class, function (User $user, Faker $faker) {
    // カードやアイテム、デッキ、クエスト履歴も持たせる。デッキは選択中にする
    $user->cards()->save(factory(UserCard::class)->make());
    $user->items()->save(factory(UserItem::class)->states('stamina')->make());
    $user->quests()->save(factory(UserQuest::class)->make());
    $userDeck = factory(UserDeck::class)->create(['user_id' => $user->id]);
    $user->last_selected_deck_id = $userDeck->id;
    $user->save();
});

$factory->afterCreatingState(User::class, 'allcards', function (User $user, Faker $faker) {
    $user->cards()->saveMany([
        factory(UserCard::class)->states('card1')->make(),
        factory(UserCard::class)->states('card2')->make(),
        factory(UserCard::class)->states('card3')->make(),
    ]);
});

$factory->afterCreatingState(User::class, 'allitems', function (User $user, Faker $faker) {
    $user->items()->saveMany([
        factory(UserItem::class)->states('exp')->make(),
        factory(UserItem::class)->states('material')->make(),
    ]);
});

$factory->afterCreatingState(User::class, 'allgifts', function (User $user, Faker $faker) {
    $user->gifts()->saveMany([
        factory(UserGift::class)->states('gameCoin')->make(),
        factory(UserGift::class)->states('specialCoin')->make(),
        factory(UserGift::class)->states('card')->make(),
        factory(UserGift::class)->states('item')->make(),
    ]);
});