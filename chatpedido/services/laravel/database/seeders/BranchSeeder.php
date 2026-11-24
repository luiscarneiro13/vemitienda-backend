<?php

namespace Database\Seeders;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::query()->where('slug', 'acme-demo')->first();

        if (! $company) {
            return;
        }

        Branch::query()->withoutTenancy()->firstOrCreate(
            ['company_id' => $company->id, 'name' => 'Sede Central'],
            [
                'address' => 'Av. Principal 123',
                'city' => 'Madrid',
                'country' => $company->country ?? 'ES',
                'timezone' => $company->timezone ?? 'Europe/Madrid',
                'is_main' => true,
                'status' => 'active',
            ]
        );
    }
}
