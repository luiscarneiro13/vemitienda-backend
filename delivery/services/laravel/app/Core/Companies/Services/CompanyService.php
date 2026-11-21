<?php

namespace App\Core\Companies\Services;

use App\Core\Companies\Actions\ActivateCompanyAction;
use App\Core\Companies\Actions\CreateCompanyAction;
use App\Core\Companies\Actions\DeactivateCompanyAction;
use App\Core\Companies\Actions\DeleteCompanyAction;
use App\Core\Companies\Actions\UpdateCompanyAction;
use App\Core\Companies\Models\Company;
use App\Core\Companies\Repositories\CompanyRepository;
use App\Core\Tenancy\TenantContext;

class CompanyService
{
    public function __construct(
        private readonly CompanyRepository $companies,
        private readonly TenantContext $tenant,
        private readonly CreateCompanyAction $createAction,
        private readonly UpdateCompanyAction $updateAction,
        private readonly ActivateCompanyAction $activateAction,
        private readonly DeactivateCompanyAction $deactivateAction,
        private readonly DeleteCompanyAction $deleteAction,
    ) {}

    public function create(array $data): Company
    {
        return $this->createAction->execute($data);
    }

    public function update(Company $company, array $data): Company
    {
        return $this->updateAction->execute($company, $data);
    }

    public function activate(Company $company): Company
    {
        return $this->activateAction->execute($company);
    }

    public function deactivate(Company $company): Company
    {
        return $this->deactivateAction->execute($company);
    }

    public function delete(Company $company): bool
    {
        return $this->deleteAction->execute($company);
    }

    public function find(int $id): ?Company
    {
        return $this->companies->find($id);
    }

    /**
     * Empresa activa según el TenantContext.
     */
    public function current(): ?Company
    {
        return $this->tenant->company();
    }
}
