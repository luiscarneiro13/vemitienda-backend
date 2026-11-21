<?php

namespace Database\Seeders;

use App\Core\Companies\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::query()->firstOrCreate(
            ['slug' => 'acme-demo'],
            [
                'name' => 'Acme Demo Company',
                'legal_name' => 'Acme Demo Company S.A.',
                'email' => 'contacto@acme-demo.test',
                'country' => 'ES',
                'timezone' => 'Europe/Madrid',
                'language' => 'es',
                'currency' => 'EUR',
                'status' => 'active',
                'is_active' => true,
            ]
        );
    }
}
