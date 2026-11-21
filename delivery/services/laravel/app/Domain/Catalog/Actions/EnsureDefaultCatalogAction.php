<?php

namespace App\Domain\Catalog\Actions;

use App\Core\Companies\Models\Company;
use App\Domain\Catalog\Models\Catalog;
use Illuminate\Support\Facades\DB;

class EnsureDefaultCatalogAction
{
    public function execute(Company $company): Catalog
    {
        return DB::transaction(function () use ($company) {
            $existing = Catalog::withoutTenancy()
                ->where('company_id', $company->id)
                ->where('is_default', true)
                ->lockForUpdate()
                ->first();

            return $existing ?? Catalog::withoutTenancy()->create([
                'company_id' => $company->id,
                'name' => 'Catálogo de '.$company->name,
                'is_default' => true,
                'is_active' => true,
            ]);
        });
    }
}
