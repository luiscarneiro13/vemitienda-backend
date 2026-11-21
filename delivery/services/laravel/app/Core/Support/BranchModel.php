<?php

namespace App\Core\Support;

use App\Core\Traits\HasBranch;

/**
 * Base para modelos de negocio que, además de pertenecer a una empresa
 * (TenantModel), también se asocian a una sucursal concreta.
 */
abstract class BranchModel extends TenantModel
{
    use HasBranch;
}
