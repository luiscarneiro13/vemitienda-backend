<?php

namespace Database\Factories;

use App\Core\Companies\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'legal_name' => $this->faker->company().' S.A.',
            'tax_id' => $this->faker->numerify('########'),
            'email' => $this->faker->companyEmail(),
            'phone' => $this->faker->phoneNumber(),
            'country' => $this->faker->countryCode(),
            'timezone' => 'UTC',
            'language' => 'es',
            'currency' => 'USD',
            'status' => 'active',
            'is_active' => true,
        ];
    }
}
