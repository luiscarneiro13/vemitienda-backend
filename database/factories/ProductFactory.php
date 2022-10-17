<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Category;
use App\Models\Product;
use App\User;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'name' => $faker->userName(),
        'price' => $faker->randomNumber(2),
        'description' => $faker->text(1000),
        'category_id' => factory(Category::class),
        'user_id' => factory(User::class),
        'share' => $faker->randomElement([0, 1])
    ];
});
