<?php

namespace Database\Factories;

use App\Core\Companies\Models\Company;
use App\Domain\Catalog\Models\CatalogCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CatalogCategory>
 */
class CatalogCategoryFactory extends Factory
{
    protected $model = CatalogCategory::class;

    public function definition(): array
    {
        return [
            'catalog_id' => fn () => Company::factory()->create()->defaultCatalog()->withoutTenancy()->value('id'),
            'name' => $this->faker->unique()->word(),
            'sort_order' => 0,
            'is_active' => true,
        ];
    }
}
