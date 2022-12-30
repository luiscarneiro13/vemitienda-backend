<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlanFactory extends Factory
{

    protected $model = Plan::class;

    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['Basico', 'Intermedio', 'Avanzado', 'Plus']),
            'price' => $this->faker->randomNumber(2),
            'quantity' => $this->faker->randomNumber(1)
        ];
    }
}
