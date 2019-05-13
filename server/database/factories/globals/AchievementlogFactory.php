<?php

use Faker\Generator as Faker;
use Carbon\Carbon;
use App\Models\Globals\Achievementlog;

$factory->define(Achievementlog::class, function (Faker $faker) {
    // ※ テーブル定義上、user_idは別途外部から渡してやる必要あり
    return [
        'achievement_id' => $faker->randomElement([10000, 20000]),
        // ※ nowは変更されている可能性があるのでCarbonから取る
        'created_at' => $faker->dateTime(Carbon::now()),
    ];
});
