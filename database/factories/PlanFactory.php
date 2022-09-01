<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Plan;
use Faker\Generator as Faker;

$factory->define(Plan::class, function (Faker $faker) {
    return [
        'name' => $faker->randomElement(['Basico', 'Intermedio', 'Avanzado', 'Plus']),
        'price' => $faker->randomNumber(2),
        'quantity' => $faker->randomNumber(1)
    ];
});
