<?php

use Faker\Generator as Faker;
use App\Models\Globals\UserCard;

$factory->define(UserCard::class, function (Faker $faker) {
    // ※ テーブル定義上、user_idは別途外部から渡してやる必要あり
    return [
        'card_id' => $faker->randomElement([1000, 1100, 2000]),
        'count' => $faker->numberBetween(1, 3),
        'exp' => $faker->numberBetween(0, 1000),
    ];
});

$factory->state(UserCard::class, 'card1', [
    'card_id' => 1000,
]);

$factory->state(UserCard::class, 'card2', [
    'card_id' => 1100,
]);

$factory->state(UserCard::class, 'card3', [
    'card_id' => 2000,
]);