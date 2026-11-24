<?php

namespace Database\Seeders;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::query()->where('slug', 'acme-demo')->first();
        $branch = $company
            ? Branch::query()->withoutTenancy()->where('company_id', $company->id)->where('is_main', true)->first()
            : null;

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'company_id' => $company?->id,
                'current_company_id' => $company?->id,
                'current_branch_id' => $branch?->id,
            ]
        );

        if ($company) {
            $company->users()->syncWithoutDetaching([$admin->id => ['role' => 'owner']]);
        }

        $admin->syncRoles(['super_admin']);

        $user = User::query()->firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Usuario Normal',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $user->syncRoles(['user']);
    }
}
