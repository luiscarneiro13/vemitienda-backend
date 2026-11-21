<?php

namespace App\Core\Companies\Actions;

use App\Core\Companies\Models\Company;
use App\Core\Companies\Repositories\CompanyRepository;

class UpdateCompanyAction
{
    public function __construct(private readonly CompanyRepository $companies) {}

    public function execute(Company $company, array $data): Company
    {
        return $this->companies->update($company, $data);
    }
}
