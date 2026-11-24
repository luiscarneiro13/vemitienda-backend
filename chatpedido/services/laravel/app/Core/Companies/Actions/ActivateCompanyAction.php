<?php

namespace App\Core\Companies\Actions;

use App\Core\Companies\Events\CompanyActivated;
use App\Core\Companies\Models\Company;

class ActivateCompanyAction
{
    public function execute(Company $company): Company
    {
        $company->update(['is_active' => true, 'status' => 'active']);

        CompanyActivated::dispatch($company);

        return $company;
    }
}
