<?php

use Faker\Generator as Faker;
use App\Models\Admins\Administrator;

$factory->define(Administrator::class, function (Faker $faker) {
    return [
        'email' => $faker->unique()->safeEmail,
        'role' => $faker->numberBetween(0, 2),
        'password' => bcrypt($faker->password),
        'note' => $faker->text(),
        'remember_token' => str_random(10),
    ];
});
