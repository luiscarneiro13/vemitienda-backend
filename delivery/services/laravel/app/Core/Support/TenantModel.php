<?php

namespace App\Core\Support;

use App\Core\Traits\HasCompany;

/**
 * Base para cualquier modelo de negocio que debe aislarse automáticamente
 * por empresa (multitenancy). Aplica HasCompany, que añade el CompanyScope
 * y autoasigna company_id — no requiere que el desarrollador repita el
 * trait manualmente en cada módulo nuevo.
 */
abstract class TenantModel extends CoreModel
{
    use HasCompany;
}
