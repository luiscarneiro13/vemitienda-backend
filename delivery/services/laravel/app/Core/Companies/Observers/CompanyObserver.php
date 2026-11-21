<?php

namespace App\Core\Companies\Observers;

use App\Core\Companies\Events\CompanyCreated;
use App\Core\Companies\Events\CompanyDeleted;
use App\Core\Companies\Events\CompanyUpdated;
use App\Core\Companies\Models\Company;
use App\Core\Timeline\TimelineService;

class CompanyObserver
{
    public function __construct(private readonly TimelineService $timeline) {}

    public function created(Company $company): void
    {
        CompanyCreated::dispatch($company);

        $this->timeline->log('company.created', "Se creó la empresa \"{$company->name}\"", $company);
    }

    public function updated(Company $company): void
    {
        CompanyUpdated::dispatch($company);

        $this->timeline->log('company.updated', "Se actualizó la empresa \"{$company->name}\"", $company);
    }

    public function deleted(Company $company): void
    {
        CompanyDeleted::dispatch($company);

        $this->timeline->log('company.deleted', "Se eliminó la empresa \"{$company->name}\"", $company);
    }
}
