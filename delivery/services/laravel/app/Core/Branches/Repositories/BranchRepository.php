<?php

namespace App\Core\Branches\Repositories;

use App\Core\Branches\Models\Branch;
use App\Core\Companies\Models\Company;
use Illuminate\Database\Eloquent\Collection;

class BranchRepository
{
    public function find(int $id): ?Branch
    {
        return Branch::find($id);
    }

    public function forCompany(Company $company): Collection
    {
        return Branch::query()->withoutTenancy()->where('company_id', $company->id)->get();
    }

    public function create(array $data): Branch
    {
        return Branch::create($data);
    }

    public function update(Branch $branch, array $data): Branch
    {
        $branch->update($data);

        return $branch->refresh();
    }

    public function delete(Branch $branch): bool
    {
        return (bool) $branch->delete();
    }
}
