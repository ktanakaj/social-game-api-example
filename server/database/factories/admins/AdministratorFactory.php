<?php

use Faker\Generator as Faker;
use App\Enums\AdminRole;
use App\Models\Admins\Administrator;

$factory->define(Administrator::class, function (Faker $faker) {
    return [
        'email' => $faker->unique()->safeEmail,
        'role' => $faker->randomElement(AdminRole::values()),
        'password' => bcrypt($faker->password),
        'note' => $faker->text(),
        'remember_token' => str_random(10),
    ];
});
