<?php

use Faker\Generator as Faker;
use App\Models\Globals\Questlog;

$factory->define(Questlog::class, function (Faker $faker) {
    // ※ テーブル定義上、user_idは別途外部から渡してやる必要あり
    return [
        'quest_id' => 1,
        'status' => $faker->randomElement(['started', 'succeed', 'failed']),
    ];
});
