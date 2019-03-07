<?php

use Faker\Generator as Faker;
use App\Enums\ObjectType;
use App\Models\Globals\UserGift;

$factory->define(UserGift::class, function (Faker $faker) {
    // ※ テーブル定義上、user_idは別途外部から渡してやる必要あり
    return [
        'text_id' => 'GIFT_MESSAGE_COVERING',
        'object_type' => $faker->randomElement([ObjectType::GAME_COIN, ObjectType::SPECIAL_COIN]),
        'object_id' => null,
        'count' => $faker->numberBetween(100, 1000),
    ];
});

$factory->state(UserGift::class, 'gameCoin', function (Faker $faker) {
    return [
        'object_type' => ObjectType::GAME_COIN,
        'object_id' => null,
        'count' => $faker->numberBetween(1000, 10000),
    ];
});

$factory->state(UserGift::class, 'specialCoin', function (Faker $faker) {
    return [
        'object_type' => ObjectType::SPECIAL_COIN,
        'object_id' => null,
        'count' => $faker->numberBetween(10, 100),
    ];
});

$factory->state(UserGift::class, 'card', function (Faker $faker) {
    return [
        'object_type' => ObjectType::CARD,
        'object_id' => $faker->randomElement([1000, 1100, 2000]),
        'count' => $faker->numberBetween(1, 3),
    ];
});

$factory->state(UserGift::class, 'item', function (Faker $faker) {
    return [
        'object_type' => ObjectType::ITEM,
        'object_id' => $faker->randomElement([100, 110, 200]),
        'count' => $faker->numberBetween(1, 10),
    ];
});
