<?php

namespace Database\Factories;

use App\Domain\Catalog\Models\CatalogCategory;
use App\Domain\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'catalog_category_id' => CatalogCategory::factory(),
            'catalog_id' => fn (array $attributes) => CatalogCategory::withoutTenancy()->whereKey($attributes['catalog_category_id'])->value('catalog_id'),
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 1, 30),
            'is_active' => true,
            'is_available' => true,
            'sort_order' => 0,
        ];
    }
}
