<?php

use Faker\Generator as Faker;
use App\Models\Globals\UserAchievement;

$factory->define(UserAchievement::class, function (Faker $faker) {
    // ※ テーブル定義上、user_idは別途外部から渡してやる必要あり
    // ※ achievement_id はユニークキーなので2件目以降は要上書き
    return [
        'achievement_id' => $faker->randomElement([10000, 20000]),
        'score' => $faker->numberBetween(1, 20),
    ];
});

$factory->state(UserAchievement::class, 'level5', [
    'achievement_id' => 1,
    'score' => 5,
]);
