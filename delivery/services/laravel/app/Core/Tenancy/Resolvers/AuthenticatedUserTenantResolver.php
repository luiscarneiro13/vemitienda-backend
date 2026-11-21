<?php

namespace App\Core\Tenancy\Resolvers;

use App\Core\Companies\Models\Company;
use App\Core\Tenancy\Contracts\TenantResolverInterface;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Estrategia por defecto: resuelve la empresa activa desde
 * `current_company_id` del usuario autenticado.
 */
class AuthenticatedUserTenantResolver implements TenantResolverInterface
{
    public function key(): string
    {
        return 'user';
    }

    public function resolve(): ?Company
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            return null;
        }

        return $user->currentCompany;
    }
}
