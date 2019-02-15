<?php

use Faker\Generator as Faker;
use Carbon\Carbon;
use App\Models\Globals\User;

$factory->define(User::class, function (Faker $faker) {
    return [
        'token' => str_random(10),
        'name' => $faker->name,
        'game_coins' => $faker->numberBetween(1000, 1000000),
        'special_coins' => $faker->numberBetween(0, 1000),
        'free_special_coins' => $faker->numberBetween(10, 10000),
        'exp' => $faker->numberBetween(100, 1000000),
        'stamina' => $faker->numberBetween(10, 100),
        // ※ nowは変更されている可能性があるのでCarbonから取る
        'last_login' => $faker->dateTime(Carbon::now()),
    ];
});
