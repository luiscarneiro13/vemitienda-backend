<?php

namespace Database\Factories;

use App\Core\Companies\Models\Company;
use App\Domain\Catalog\Models\Catalog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Catalog>
 */
class CatalogFactory extends Factory
{
    protected $model = Catalog::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => 'Catálogo secundario',
            'is_default' => false,
            'is_active' => true,
        ];
    }
}
