<?php

namespace Database\Factories;

use App\Domain\Catalog\Models\ProductOption;
use App\Domain\Catalog\Models\ProductOptionGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductOption>
 */
class ProductOptionFactory extends Factory
{
    protected $model = ProductOption::class;

    public function definition(): array
    {
        return [
            'product_option_group_id' => ProductOptionGroup::factory(),
            'name' => $this->faker->word(),
            'price' => 0,
            'is_active' => true,
        ];
    }
}
