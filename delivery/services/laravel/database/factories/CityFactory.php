<?php

namespace Database\Factories;

use App\Domain\Locations\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<City>
 */
class CityFactory extends Factory
{
    protected $model = City::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->city(),
            'state' => $this->faker->state(),
            'country' => 'VE',
            'is_active' => true,
        ];
    }
}
