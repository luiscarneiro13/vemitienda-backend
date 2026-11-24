<?php

namespace App\Core\Companies\Actions;

use App\Core\Companies\Models\Company;
use App\Core\Companies\Repositories\CompanyRepository;

class DeleteCompanyAction
{
    public function __construct(private readonly CompanyRepository $companies) {}

    public function execute(Company $company): bool
    {
        return $this->companies->delete($company);
    }
}
