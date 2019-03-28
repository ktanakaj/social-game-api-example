<?php

use Faker\Generator as Faker;
use App\Enums\ObjectType;
use App\Models\Globals\Gachalog;

$factory->define(Gachalog::class, function (Faker $faker) {
    // ※ テーブル定義上、user_idは別途外部から渡してやる必要あり
    return [
        'gacha_id' => 1,
        'gacha_price_id' => 101,
    ];
});

$factory->afterCreating(Gachalog::class, function (Gachalog $log, Faker $faker) {
    $log->drops()->create([
        'object_type' => ObjectType::CARD,
        'object_id' => 1000,
        'count' => 1,
    ]);
});
