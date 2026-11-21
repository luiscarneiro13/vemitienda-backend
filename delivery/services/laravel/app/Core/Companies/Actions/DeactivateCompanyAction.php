<?php

namespace App\Core\Companies\Actions;

use App\Core\Companies\Events\CompanyDeactivated;
use App\Core\Companies\Models\Company;

class DeactivateCompanyAction
{
    public function execute(Company $company): Company
    {
        $company->update(['is_active' => false, 'status' => 'inactive']);

        CompanyDeactivated::dispatch($company);

        return $company;
    }
}
