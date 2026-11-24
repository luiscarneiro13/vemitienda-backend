<?php

namespace Database\Factories;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Branch>
 */
class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'name' => $this->faker->city().' Branch',
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'city_id' => null,
            'country' => $this->faker->countryCode(),
            'timezone' => 'UTC',
            'phone' => $this->faker->phoneNumber(),
            'whatsapp_number' => '58414'.$this->faker->numerify('#######'),
            'email' => $this->faker->companyEmail(),
            'is_main' => false,
            'status' => 'active',
        ];
    }
}
