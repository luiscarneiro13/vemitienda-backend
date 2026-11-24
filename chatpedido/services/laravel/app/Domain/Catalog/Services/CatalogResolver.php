<?php

namespace App\Domain\Catalog\Services;

use App\Core\Branches\Models\Branch;
use App\Domain\Catalog\Models\Catalog;

/**
 * Decide qué catálogo ve una sucursal.
 * Hoy: el catálogo por defecto y activo de la tienda.
 * Futuro (solo agregando tablas): 1) catalog_branch, 2) catalog_city, 3) defecto de la tienda.
 */
class CatalogResolver
{
    public function forBranch(Branch $branch): ?Catalog
    {
        return Catalog::withoutTenancy()
            ->where('company_id', $branch->company_id)
            ->where('is_default', true)
            ->where('is_active', true)
            ->first();
    }
}
