<?php

namespace App\Core\Companies\Repositories;

use App\Core\Companies\Models\Company;
use Illuminate\Database\Eloquent\Collection;

class CompanyRepository
{
    public function find(int $id): ?Company
    {
        return Company::find($id);
    }

    public function findByUuid(string $uuid): ?Company
    {
        return Company::where('uuid', $uuid)->first();
    }

    public function findBySlug(string $slug): ?Company
    {
        return Company::where('slug', $slug)->first();
    }

    public function all(): Collection
    {
        return Company::all();
    }

    public function create(array $data): Company
    {
        return Company::create($data);
    }

    public function update(Company $company, array $data): Company
    {
        $company->update($data);

        return $company->refresh();
    }

    public function delete(Company $company): bool
    {
        return (bool) $company->delete();
    }
}
