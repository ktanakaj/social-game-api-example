<?php

use Faker\Generator as Faker;
use App\Models\Globals\UserQuest;

$factory->define(UserQuest::class, function (Faker $faker) {
    // ※ テーブル定義上、user_idは別途外部から渡してやる必要あり
    // ※ quest_id はユニークキーなので2件目以降は要上書き
    return [
        'quest_id' => 1,
        'count' => $faker->numberBetween(1, 3),
    ];
});
