<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{

    protected $model = Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->userName(),
            'price' => $this->faker->randomNumber(2),
            'description' => $this->faker->text(1000),
            'category_id' => Category::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
            'share' => $this->faker->randomElement([0, 1])
        ];
    }
}
