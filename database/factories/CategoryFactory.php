<?php

namespace Database\Factories;

use App\Models\Category;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{

    protected $model = Category::class;

    public function definition()
    {
        return [
            'user_id' => 1,
            'name' => $this->faker->lastName,
        ];
        // return [
        //     'user_id' => User::inRandomOrder()->first()->id,
        //     'name' => $this->faker->lastName,
        // ];
    }
}
