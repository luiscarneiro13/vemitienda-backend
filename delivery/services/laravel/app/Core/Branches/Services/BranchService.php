<?php

namespace App\Core\Branches\Services;

use App\Core\Branches\Models\Branch;
use App\Core\Branches\Repositories\BranchRepository;
use App\Core\Companies\Models\Company;
use App\Core\Tenancy\TenantContext;
use Illuminate\Database\Eloquent\Collection;

class BranchService
{
    public function __construct(
        private readonly BranchRepository $branches,
        private readonly TenantContext $tenant,
    ) {}

    public function create(array $data): Branch
    {
        return $this->branches->create($data);
    }

    public function update(Branch $branch, array $data): Branch
    {
        return $this->branches->update($branch, $data);
    }

    public function delete(Branch $branch): bool
    {
        return $this->branches->delete($branch);
    }

    public function find(int $id): ?Branch
    {
        return $this->branches->find($id);
    }

    public function forCompany(Company $company): Collection
    {
        return $this->branches->forCompany($company);
    }

    /**
     * Sucursal activa según el TenantContext.
     */
    public function current(): ?Branch
    {
        return $this->tenant->branch();
    }
}
