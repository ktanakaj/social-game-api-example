<?php

use Faker\Generator as Faker;
use App\Models\Globals\User;

$factory->define(User::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'token' => str_random(10),
    ];
});
