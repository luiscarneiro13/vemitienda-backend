<?php

namespace App\Core\Support;

/**
 * Base semántica para el propio modelo Company y para catálogos globales
 * asociados a la empresa que NO deben auto-scopearse por company_id (el
 * scope se aplicaría sobre sí mismos, lo cual no tiene sentido). Distinto
 * de TenantModel, que sí aplica HasCompany.
 */
abstract class CompanyModel extends CoreModel
{
    //
}
