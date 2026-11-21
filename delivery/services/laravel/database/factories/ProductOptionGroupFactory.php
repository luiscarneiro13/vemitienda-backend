<?php

namespace Database\Factories;

use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\ProductOptionGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductOptionGroup>
 */
class ProductOptionGroupFactory extends Factory
{
    protected $model = ProductOptionGroup::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'name' => 'Tamaño',
            'selection_type' => 'single',
            'is_required' => false,
        ];
    }
}
