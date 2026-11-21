<?php

namespace App\Core\Companies\Actions;

use App\Core\Companies\Models\Company;
use App\Core\Companies\Repositories\CompanyRepository;

class CreateCompanyAction
{
    public function __construct(private readonly CompanyRepository $companies) {}

    public function execute(array $data): Company
    {
        return $this->companies->create($data);
    }
}
