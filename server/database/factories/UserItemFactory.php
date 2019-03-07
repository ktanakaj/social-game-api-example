<?php

use Faker\Generator as Faker;
use App\Models\Globals\UserItem;

$factory->define(UserItem::class, function (Faker $faker) {
    // ※ テーブル定義上、user_idは別途外部から渡してやる必要あり
    // ※ item_id はユニークキーなので2件目以降は要上書き
    return [
        'item_id' => $faker->randomElement([100, 110, 200]),
        'count' => $faker->numberBetween(1, 20),
    ];
});

$factory->state(UserItem::class, 'stamina', [
    'item_id' => 100,
]);

$factory->state(UserItem::class, 'exp', [
    'item_id' => 110,
]);

$factory->state(UserItem::class, 'material', [
    'item_id' => 200,
]);
